<?php

namespace App\Http\Controllers\FranchiseStaffController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\FranchiseEventItem;
use App\Models\FgpItem;
use App\Models\Customer;
use App\Models\FgpOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Enums\FgpOrders;    

class FranchiseStaffController extends Controller
{
    public function dashboard()
    {
        return view('franchise_staff.dashboard');
    }

    public function calendar(){
        $events = Event::where('franchise_id' , Auth::user()->franchise_id)->get();
        $badgeEvents = Event::where('franchise_id' , Auth::user()->franchise_id)->orderBy('created_at', 'DESC')
        ->get();

        // Group by year and month
        $distinctEvents = $badgeEvents->groupBy(function ($event) {
            return Carbon::parse($event->created_at)->format('Y-m'); // Group by Year-Month (e.g., 2025-05)
        });

        // Take the first event of each group
        $uniqueEvents = $distinctEvents->map(function ($group) {
            return $group->first(); // Get the first event in each group
        });

        return view('franchise_staff.event.calender' , compact('events','uniqueEvents'));
    }

    public function report(Request $request) {
        $monthYear = $request->input('month_year', Carbon::now()->format('Y-m'));

        // Extract year and month from the provided monthYear
        $year = Carbon::parse($monthYear)->year;
        $month = Carbon::parse($monthYear)->month;

        // Fetch the data based on the selected or default month/year
  $eventItems = FranchiseEventItem::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereHas('events', function ($query) {
                $query->where('franchise_id', Auth::user()->franchise_id);
            })
            ->get();
        return view('franchise_staff.event.report', compact('eventItems'));
    }

    public function eventView($id){
        $event = Event::where('id' , $id)->firstorfail();
        $eventItems = FranchiseEventItem::where('event_id' , $event->id)->get();
        return view('franchise_staff.event.view' , compact('event','eventItems'));
    }


    public function flavors(Request $request, $franchise_id){

        $deliveredOrders = FgpOrder::where('status', FgpOrders::DELIVERED->value)->where('franchise_id' , $franchise_id)->count();
       
        $shippedOrders = FgpOrder::where('status', FgpOrders::SHIPPED->value)->where('franchise_id' , $franchise_id)->count();
        $paidOrders = FgpOrder::where('status', FgpOrders::PAID->value)->where('franchise_id' , $franchise_id)->count();
        $pendingOrders = FgpOrder::where('status', 'pending')->where('franchise_id' , $franchise_id)->count();

        // dd($deliveredOrders,$shippedOrders,$paidOrders,$pendingOrders);
        $orders = FgpOrder::where('franchise_id' , $franchise_id)->get();

        $totalOrders = $orders->count();

        if ($request->ajax()) {
            $query = FgpOrder::with(['franchise', 'customer'])
                ->where('franchise_id', $franchise_id);

            return DataTables::of($query)
                ->addColumn('user_name', function ($order) use ($franchise_id) {
                    // Try to get franchise from relationship first, then fallback to direct query
                    if ($order->franchise) {
                        return $order->franchise->business_name;
                    }
                    
                    // Fallback: direct query
                    $franchise = \App\Models\Franchise::find($franchise_id);
                    return $franchise ? $franchise->business_name : 'N/A';
                })
                ->addColumn('items_ordered', function ($order) {
                    $itemCount = DB::table('fgp_order_items')->where('fgp_order_id', $order->id)->count();
                    return '<span class="cursor-pointer text-primary order-detail-trigger" data-id="' . $order->id . '">' . $itemCount . ' items</span>';
                })
                ->addColumn('status', function ($order) {
                    return ucfirst($order->status ?? 'pending');
                })
                ->addColumn('total_price', function ($order) {
                    $totalAmount = DB::table('fgp_order_items')
                        ->where('fgp_order_id', $order->id)
                        ->selectRaw('SUM(quantity * unit_price) as total')
                        ->value('total');
                    return '$' . number_format($totalAmount ?? 0, 2);
                })
                ->addColumn('order_date', function ($order) {
                    return $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A') : 'N/A';
                })
                ->filterColumn('user_name', function ($query, $keyword) {
                    $query->whereHas('franchise', function ($q) use ($keyword) {
                        $q->where('business_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('status', function ($query, $keyword) {
                    $query->where('status', 'like', "%{$keyword}%");
                })
                ->filterColumn('order_date', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->whereDate('created_at', 'like', "%{$keyword}%")
                          ->orWhere('created_at', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['items_ordered'])
                ->make(true);
        }
       

        return view('franchise_staff.flavors.index', compact('deliveredOrders', 'shippedOrders', 'pendingOrders','paidOrders', 'orders', 'totalOrders','franchise_id'));
    }


    public function index() {
       
        $data['customers'] = Customer::where('franchise_id' , Auth::user()->franchise_id)->get();
        $data['customerCount'] = Customer::where('franchise_id' , Auth::user()->franchise_id)->count();
        return view('franchise_staff.customer.index' ,$data);
    }

    public function create( $franchise ) {
        return view('franchise_staff.customer.create', compact('franchise'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'nullable|numeric|digits_between:8,16',
            'email' => 'nullable|email|max:191',
    'state' => 'nullable|alpha|size:2', // 2-letter state code (alphabetic)
    'zip_code' => 'nullable|digits:5', // 5 digits zip code

            'address1' => 'nullable|max:191',
            'address2' => 'nullable|max:191',
            'notes' => 'nullable|max:191',
        ]);

        $customer = Customer::create([
            'franchise_id' => Auth::user()->franchise_id,
            'user_id' => Auth::user()->user_id ?? 0,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'notes' => $request->notes,
        ]);


        return redirect()->route('franchise_staff.customer')->with('success' , 'Customer created successfully');
    }

    public function edit($id) {
        $data['customer'] = Customer::where('id' , $id)->firstorfail();
        return view('franchise_staff.customer.edit' , $data);
    }

    public function update(Request $request , $id){
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'nullalbe|numeric|digits_between:8,16',
            'email' => 'nullalbe|email|max:191',
    'zip_code' => 'nullalbe|digits:5', // 5 digits zip code
    'state' => 'nullalbe|alpha|size:2', // 2-letter state code (alphabetic)

            'address1' => 'nullalbe|max:191',
            'address2' => 'nullable|max:191',
            'notes' => 'nullable|max:191',
        ]);

        $customer = Customer::where('id' , $id)->update([
            'franchise_id' => Auth::user()->franchise_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'notes' => $request->notes,
        ]);


        return redirect()->route('franchise_staff.customer')->with('success' , 'Customer updated successfully');
    }

    public function view($id) {
        $data['customer'] = Customer::where('customer_id' , $id)->firstorfail();
        return view('franchise_staff.customer.view' , $data);
    }

    public function delete($id) {
        $data['customer'] = Customer::where('customer_id' , $id)->delete();
        return redirect()->route('franchise_staff.customer')->with('success' , 'Customer deleted successfully');
    }

    public function flavorsDetail(Request $request)
    {
        $orderId = $request->input('id');

        $orderDetails = DB::table('fgp_order_details as od')
                    ->join('fgp_items as fi', 'od.fgp_item_id', '=', 'fi.id')
        ->where('od.fgp_order_id', $orderId)
        ->select('od.*', 'fi.name')
        ->get();

    foreach ($orderDetails as $detail) {
        $detail->formatted_date = Carbon::parse($detail->date_transaction)->format('M d, Y h:i A');
    }


        return response()->json([
            'orderDetails' => $orderDetails,
        ]);
    }
}
