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
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index($franchisee)
    {
     
        $today = Carbon::today()->toDateString();
        $events = Event::where('franchisee_id', $franchisee)->get();
        return view('franchise_admin.event.index', compact('events'));
    }

    public function eventCalender($franchisee)
    {
        $events = Event::where('franchisee_id', $franchisee)->get();
        $badgeEvents = Event::where('franchisee_id', $franchisee)
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
        return view('franchise_admin.event.calender', compact('events', 'uniqueEvents'));
    }

    public function updateStatus(Request $request, $franchisee)
    {
        $event = Event::where('franchisee_id', $franchisee)->find($request->event_id);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        $event->event_status = $request->status;
        $event->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function view($franchisee, $id)
    {
        $event = Event::where('franchisee_id', $franchisee)->where('id', $id)->firstorfail();
        $eventItems = FranchiseEventItem::where('event_id', $event->id)->get();
        return view('franchise_admin.event.view', compact('event', 'eventItems'));
    }

    public function report(Request $request, $franchisee)
    {
        if ($request->ajax()) {
            $monthYear = $request->input('month_year', Carbon::now()->format('Y-m'));
            $year = Carbon::parse($monthYear)->year;
            $month = Carbon::parse($monthYear)->month;

            $query = FranchiseEventItem::with(['fgpItem'])
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereHas('events', function ($query) use ($franchisee) {
                    $query->where('franchisee_id', $franchisee);
                });

            return datatables()
                ->eloquent($query)
                ->addColumn('orderable_flover', function ($eventItem) {
                    return $eventItem->fgpItem->name ?? '-';
                })
                ->addColumn('quantity', function ($eventItem) {
                    return $eventItem->quantity ?: '-';
                })
                ->addColumn('on_hand_flover', function ($eventItem) use ($request) {
                    $orderable = \DB::table('fgp_order_details')
                        ->where('id', $eventItem->orderable)
                        ->first();
                    
                    $fgpItem = isset($orderable->fgp_item_id)
                        ? \App\Models\FgpItem::where('fgp_item_id', $orderable->fgp_item_id)->first()
                        : null;
                    
                    return $fgpItem->name ?? '-';
                })
                ->addColumn('on_hand_quantity', function ($eventItem) {
                    $orderable = \DB::table('fgp_order_details')
                        ->where('id', $eventItem->orderable)
                        ->first();
                    
                    return isset($orderable->unit_number) ? $orderable->unit_number : '-';
                })
                ->addColumn('shortage_overage', function ($eventItem) {
                    $orderable = \DB::table('fgp_order_details')
                        ->where('id', $eventItem->orderable)
                        ->first();
                    
                    return isset($orderable->unit_number, $eventItem->quantity) 
                        ? $orderable->unit_number - $eventItem->quantity 
                        : '';
                })
                ->addColumn('month_available', function ($eventItem) {
                    $pop = null;
                    if (isset($eventItem->in_stock)) {
                        $pop = \App\Models\FgpItem::where('fgp_item_id', $eventItem->in_stock)->first();
                    }
                    
                    if ($pop && $pop->created_at) {
                        return \Carbon\Carbon::parse($pop->created_at)->month == now()->month
                            ? \Carbon\Carbon::parse($pop->created_at)->format('d M Y')
                            : '-';
                    }
                    return '-';
                })
                ->rawColumns(['orderable_flover', 'quantity', 'on_hand_flover', 'on_hand_quantity', 'shortage_overage', 'month_available'])
                ->make(true);
        }

        return view('franchise_admin.event.report');
    }

    public function create($franchisee)
    {
        $currentMonth = strval(Carbon::now()->format('n'));
        $pops = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0)
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

        $staffs = User::where('role', 'franchise_staff')->where('franchisee_id', $franchisee)->get();

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

        $customers = Customer::where('franchisee_id', $franchisee)->get();

        return view('franchise_admin.event.create', compact('pops', 'staffs', 'orderDetails', 'customers'));
    }

    public function store(Request $request, $franchisee)
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

        $event = Event::create([
            'franchisee_id' => $franchisee,
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

        foreach ($validated['orderable'] as $index => $orderableId) {
            FranchiseEventItem::create([
                'event_id' => $event->id,
                'in_stock' => $validated['in_stock'][$index] ?? null,
                'orderable' => $orderableId,
                'quantity' => $validated['quantity'][$index],
            ]);
        }

        return redirect()->route('franchise.events.calender', ['franchisee' => $franchisee])->with('success', 'Event created successfully!');
    }

    public function compare($franchisee, $event)
    {
        $event = Event::where('franchisee_id', $franchisee)->findOrFail($event);
        return view('franchise_admin.event.compare', compact('event'));
    }

    public function date(Request $request, $franchisee)
    {
        $date = $request->input('date');
        $events = Event::where('franchisee_id', $franchisee)
            ->whereDate('start_date', $date)
            ->get();

        return response()->json($events);
    }

    public function eventCalenderAdmin()
    {
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
        return view('corporate_admin.event.calender', compact('events', 'uniqueEvents'));
    }

    public function viewAdmin($id)
    {
        $event = Event::where('id', $id)->firstorfail();
        $eventItems = FranchiseEventItem::where('event_id', $event->id)->get();
        return view('corporate_admin.event.view', compact('event', 'eventItems'));
    }

    public function eventReportAdmin(Request $request)
    {
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
