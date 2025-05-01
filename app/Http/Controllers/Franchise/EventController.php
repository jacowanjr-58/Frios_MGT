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
        $badgeEvents = Event::where('franchisee_id' , Auth::user()->franchisee_id)->orderBy('created_at' , 'DESC')->get();
        return view('franchise_admin.event.calender' , compact('events','badgeEvents'));
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
            'notes' => 'nullable|string',
            'resources_selection' => 'nullable|array',
            'event_type' => 'nullable|string|max:100',
            'planned_payment' => 'nullable|in:cash,check,inovice,credit-card',
            'in_stock' => 'required|array',
            'orderable' => 'required|array',
            'quantity' => 'required|array',
            'in_stock.*' => 'required|integer',
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
                'event_notes' => $validated['notes'] ?? null,
                'resources_selection' => json_encode($validated['resources_selection'] ?? []),
                'event_type' => $validated['event_type'] ?? null,
                'planned_payment' => $validated['planned_payment'] ?? null,
            ]);

            // 3. Save Event Items
            foreach ($validated['in_stock'] as $index => $inStockId) {
                FranchiseEventItem::create([
                    'event_id' => $event->id,
                    'in_stock' => $inStockId,
                    'orderable' => $validated['orderable'][$index],
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
}
