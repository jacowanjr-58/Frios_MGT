<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FgpItem;
use App\Models\FranchiseEvent;
use App\Models\FranchiseEventItem;
use App\Models\InventoryAllocation;
use App\Models\Customer;
use App\Models\User;
use App\Models\FgpOrderDetail;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Charge;
use App\Models\EventTransaction;
use App\Mail\EventPaidMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

use DB;

class EventController extends Controller
{
    public function index() {
        // $events = FranchiseEvent::orderBy('start_date')->get();
        $today = Carbon::today()->toDateString();

        $events = Event::where('franchisee_id' , Auth::user()->franchisee_id)->get();

        // dd($events);
        return view('franchise_admin.event.index', compact('events'));
    }

    public function eventCalender() {
        $events = Event::where('franchisee_id' , Auth::user()->franchisee_id)->get();
        $badgeEvents = Event::where('franchisee_id', Auth::user()->franchisee_id)
        ->orderBy('created_at', 'DESC')
        ->get();

    // Group by year and month
    $distinctEvents = $badgeEvents->groupBy(function ($event) {
        return Carbon::parse($event->created_at)->format('Y-m'); // Group by Year-Month (e.g., 2025-05)
    });

    // Take the first event of each group
    $uniqueEvents = $distinctEvents->map(function ($group) {
        return $group->first(); // Get the first event in each group
    });
        return view('franchise_admin.event.calender' , compact('events','uniqueEvents'));
    }

    public function updateStatus(Request $request)
    {
        $event = Event::find($request->event_id);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        $event->event_status = $request->status;
        $event->save();

        return response()->json(['success' => true , 'message' => 'Status updated successfully']);
    }

    public function view($id) {
        $event = Event::where('id' , $id)->firstorfail();
        $eventItems = FranchiseEventItem::where('event_id' , $event->id)->get();
        return view('franchise_admin.event.view' , compact('event','eventItems'));
    }

    public function report(Request $request) {
        $monthYear = $request->input('month_year', Carbon::now()->format('Y-m'));

        // Extract year and month from the provided monthYear
        $year = Carbon::parse($monthYear)->year;
        $month = Carbon::parse($monthYear)->month;

        // Fetch the data based on the selected or default month/year
        $eventItems = FranchiseEventItem::whereYear('created_at', $year)
                                         ->whereMonth('created_at', $month)
                                         ->get();
        return view('franchise_admin.event.report', compact('eventItems'));
    }

    public function create() {
        $currentMonth = strval(Carbon::now()->format('n'));
        $pops = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0)
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

        $staffs = User::where('role', 'franchise_staff')->get();


        $orders = DB::table('fgp_orders')
        ->where('status', 'Delivered')
        ->get();

    $orderIds = $orders->pluck('fgp_ordersID');

    $orderDetails = DB::table('fgp_order_details')
        ->join('fgp_items', 'fgp_order_details.fgp_item_id', '=', 'fgp_items.fgp_item_id')
        ->whereIn('fgp_order_details.fgp_order_id', $orderIds)
        ->select(
            'fgp_order_details.fgp_item_id',
            'fgp_items.name as item_name',
            DB::raw('SUM(fgp_order_details.unit_number) as total_units')
        )
        ->groupBy('fgp_order_details.fgp_item_id', 'fgp_items.name')
        ->get();

        $customers = Customer::get();

        return view('franchise_admin.event.create', compact('pops','staffs','orderDetails','customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'event_status' => 'required|in:scheduled,tentative,staffed',
            'staff_assigned' => 'required|array',
            'staff_assigned.*' => 'integer',
            'customer_id' => 'nullable|integer',
            'expected_sales' => 'nullable|numeric',
            'actual_sales' => 'nullable|numeric',
            'costs' => 'nullable|numeric',
            'event_notes' => 'nullable|string',
            'resources_selection' => 'nullable|array',
            'event_type' => 'nullable|string|max:100',
            'planned_payment' => 'nullable|in:cash,check,inovice,credit-card',
            'in_stock' => 'nullable|array',
            'orderable' => 'required|array',
            'quantity' => 'required|array',
            'in_stock.*' => 'nullable|integer',
            'orderable.*' => 'required|integer',
            'quantity.*' => 'required|numeric|min:0',
        ]);

        $instock    = $request->in_stock;
        $quantities = $request->quantity;
        $orderable  = $request->orderable;


        $inStockItems = FgpItem::whereIn('fgp_item_id', $instock)->get();

        $totalCaseCost = 0;
        foreach ($inStockItems as $index => $item) {
            $qty = $quantities[$index] ?? 0;
            $itemTotal = $item->case_cost * $qty;
            $totalCaseCost += $itemTotal;
            $item->total_cost = $itemTotal;
        }

        $orderItems = FgpOrderDetail::whereIn('id', $orderable)->get();

        $itemsWithCost = $orderItems->map(function ($item) {
            $item->total_cost = $item->unit_number * $item->unit_cost;
            return $item;
        });

        $grandTotal = $itemsWithCost->sum('total_cost');

        $finalTotal = $totalCaseCost + $grandTotal;

    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

    try {
        $amountInCents = $finalTotal * 100;

        $charge = \Stripe\Charge::create([
            'amount' => $amountInCents,
            'currency' => 'usd',
            'description' => 'Order Payment by: ' . $request->cardholder_name,
            'source' => $request->stripeToken,
            'metadata' => [
                'franchisee_id' => Auth::user()->franchisee_id,
            ],
        ]);
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['Stripe Error: ' . $e->getMessage()]);
    }

            $event = \App\Models\Event::create([
                'franchisee_id' => Auth::user()->franchisee_id,
                'event_name' => $validated['event_name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'event_status' => $validated['event_status'],
                'staff_assigned' => json_encode($validated['staff_assigned'] ?? []),
                'customer_id' => $validated['customer_id'] ?? null,
                'expected_sales' => $validated['expected_sales'] ?? null,
                'actual_sales' => $validated['actual_sales'] ?? null,
                'costs' => $validated['costs'] ?? null,
                'event_notes' => $validated['event_notes'] ?? null,
                'resources_selection' => json_encode($validated['resources_selection'] ?? []),
                'event_type' => $validated['event_type'] ?? null,
                'planned_payment' => $validated['planned_payment'] ?? null,
            ]);

                \App\Models\EventTransaction::create([
                'franchisee_id' => Auth::user()->franchisee_id,
                'event_id' => $event->id,
                'cardholder_name' => $request->cardholder_name,
                'amount' => $finalTotal,
                'stripe_payment_intent_id' => $charge->id,
                'stripe_payment_method' => $charge->payment_method ?? null,
                'stripe_currency' => $charge->currency,
                'stripe_client_secret' => $charge->client_secret ?? null,
                'stripe_status' => $charge->status,
            ]);

            foreach ($validated['orderable'] as $index => $orderableId) {
                FranchiseEventItem::create([
                    'event_id' => $event->id,
                    'in_stock' => $validated['in_stock'][$index] ?? null,
                    'orderable' => $orderableId,
                    'quantity' => $validated['quantity'][$index],
                ]);
            }

        // $eventTransaction = \App\Models\EventTransaction::where('event_id', $event->id)->firstOrFail();
        // $eventItems = \App\Models\FranchiseEventItem::where('event_id', $event->id)->get();
        // $franchisee = \App\Models\Franchisee::where('franchisee_id', $event->franchisee_id)->firstOrFail();

        // $pdf = PDF::loadView('franchise_admin.payment.pdf.event-pos', compact('eventTransaction', 'franchisee', 'eventItems'));
        // $pdfPath = storage_path('app/public/event_invoice_' . $event->id . '.pdf');
        // $pdf->save($pdfPath);

        // Mail::to($franchisee->email)->send(new EventPaidMail($franchisee, $eventTransaction, $eventItems, $pdfPath));

        // unlink($pdfPath);

            return redirect()->route('franchise.events.calender')->with('success', 'Event created successfully!');

    }

    public function compare(FranchiseEvent $event) {
        $inventory = InventoryAllocation::get();
        return view('franchise_admin.event.compare', compact('event','inventory'));
        // dd($event);
    }

    public function date(Request $request){
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays < 15) {
            $pops = null;
            $message = 'No Orderable pops will be available within the 15-day duration.';
        } else {
            $pops = FgpItem::where('orderable', 1)
                ->where('internal_inventory', '>', 0)
                ->get();
            $message = null;
        }

        $orders = DB::table('fgp_orders')
            ->where('status', 'Delivered')
            ->get();

        $orderIds = $orders->pluck('fgp_ordersID');

        $orderDetails = DB::table('fgp_order_details')
            ->join('fgp_items', 'fgp_order_details.fgp_item_id', '=', 'fgp_items.fgp_item_id')
            ->whereIn('fgp_order_details.fgp_order_id', $orderIds)
            ->select(
                'fgp_order_details.id',
                'fgp_order_details.fgp_order_id',
                'fgp_order_details.fgp_item_id',
                'fgp_items.name as item_name',
                'fgp_order_details.unit_number'
            )
            ->get();



//                 return response()->json([
//  'orderDetails' => $orderDetails,
//                 'pops' => $pops,
//                 'startDate' => $startDate->toDateString(),
//                 'endDate' => $endDate->toDateString(),
//                 'orderCount' => $orders->count(),
//                 ]);

            $html = view('franchise_admin.event.flavor', [
                'orderDetails' => $orderDetails,
                'pops' => $pops,
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
                'orderCount' => $orders->count(),
            ])->render();

            // Return JSON with rendered HTML
            return response()->json([
                'success' => true,
                'html' => $html,
                'message' => $message
            ]);
    }

    public function eventCalenderAdmin(){
        $events = Event::get();
        $badgeEvents = Event::orderBy('created_at', 'DESC')
        ->get();

        // Group by year and month
        $distinctEvents = $badgeEvents->groupBy(function ($event) {
            return Carbon::parse($event->created_at)->format('Y-m'); // Group by Year-Month (e.g., 2025-05)
        });

        // Take the first event of each group
        $uniqueEvents = $distinctEvents->map(function ($group) {
            return $group->first(); // Get the first event in each group
        });
            return view('corporate_admin.event.calender' , compact('events','uniqueEvents'));
    }

    public function viewAdmin($id){
        $event = Event::where('id' , $id)->firstorfail();
        $eventItems = FranchiseEventItem::where('event_id' , $event->id)->get();
        return view('corporate_admin.event.view' , compact('event','eventItems'));
    }

    public function eventReportAdmin(Request $request) {
        $monthYear = $request->input('month_year', Carbon::now()->format('Y-m'));

        // Extract year and month from the provided monthYear
        $year = Carbon::parse($monthYear)->year;
        $month = Carbon::parse($monthYear)->month;

        // Fetch the data based on the selected or default month/year
        $eventItems = FranchiseEventItem::whereYear('created_at', $year)
                                         ->whereMonth('created_at', $month)
                                         ->get();
        return view('corporate_admin.event.report', compact('eventItems'));
    }
}
