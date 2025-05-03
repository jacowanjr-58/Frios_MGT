<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FpgItem;
use App\Models\FranchiseEvent;
use App\Models\FranchiseEventItem;
use App\Models\InventoryAllocation;
use App\Models\Customer;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
        $pops = FpgItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0)
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

        $staffs = User::where('role', 'franchise_staff')->get();


        $orders = DB::table('fpg_orders')
        ->where('status', 'Delivered')
        ->get();

    $orderIds = $orders->pluck('fgp_ordersID');

    $orderDetails = DB::table('fgp_order_details')
        ->join('fpg_items', 'fgp_order_details.fgp_item_id', '=', 'fpg_items.fgp_item_id')
        ->whereIn('fgp_order_details.fpg_order_id', $orderIds)
        ->select(
            'fgp_order_details.fgp_item_id',
            'fpg_items.name as item_name',
            DB::raw('SUM(fgp_order_details.unit_number) as total_units')
        )
        ->groupBy('fgp_order_details.fgp_item_id', 'fpg_items.name')
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
            'staff_assigned' => 'nullable|array',
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

        // try {
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

            // 3. Save Event Items
            foreach ($validated['orderable'] as $index => $orderableId) {
                FranchiseEventItem::create([
                    'event_id' => $event->id,
                    'in_stock' => $validated['in_stock'][$index] ?? null,
                    'orderable' => $orderableId,
                    'quantity' => $validated['quantity'][$index],
                ]);
            }

            return redirect()->route('franchise.events.calender')->with('success', 'Event created successfully!');
        // } catch (\Exception $e) {
        //     return redirect()->back()->with(['error' => 'Something went wrong: ' . $e->getMessage()]);
        // }
    }

    public function compare(FranchiseEvent $event) {
        $inventory = InventoryAllocation::get();
        return view('franchise_admin.event.compare', compact('event','inventory'));
        // dd($event);
    }

    public function date(Request $request){
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Calculate the difference between the start and end date
        $diffInDays = $startDate->diffInDays($endDate);

        // Check if the duration is less than 15 days and modify the $pops query accordingly
        if ($diffInDays < 15) {
            // If the duration is less than 15 days, set $pops to null and return a message
            $pops = null;
            $message = 'No Orderable pops will be available within the 15-day duration.';
        } else {
            // Fetch pops data if the duration is greater than or equal to 15 days
            $pops = FpgItem::where('orderable', 1)
                ->where('internal_inventory', '>', 0)
                ->get();
            $message = null;
        }

        // Fetch order details based on orders within the date range
        $orders = DB::table('fpg_orders')
            ->where('status', 'Delivered')
            ->get();

        $orderIds = $orders->pluck('fgp_ordersID');

        $orderDetails = DB::table('fgp_order_details')
            ->join('fpg_items', 'fgp_order_details.fgp_item_id', '=', 'fpg_items.fgp_item_id')
            ->whereIn('fgp_order_details.fpg_order_id', $orderIds)
            ->select(
                'fgp_order_details.fgp_item_id',
                'fpg_items.name as item_name',
                DB::raw('SUM(fgp_order_details.unit_number) as total_units')
            )
            ->groupBy('fgp_order_details.fgp_item_id', 'fpg_items.name')
            ->get();



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
}
