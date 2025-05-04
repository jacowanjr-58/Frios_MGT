<?php

namespace App\Http\Controllers\FranchiseStaffController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\FranchiseEventItem;
use App\Models\FpgItem;
use App\Models\Customer;
use App\Models\FpgOrder;
use Carbon\Carbon;
use Auth;
use DB;

class FranchiseStaffController extends Controller
{
    public function dashboard()
    {
        return view('franchise_staff.dashboard');
    }

    public function calendar(){
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

        return view('franchise_staff.event.calender' , compact('events','uniqueEvents'));
    }

    public function eventView($id){
        $event = Event::where('id' , $id)->firstorfail();
        $eventItems = FranchiseEventItem::where('event_id' , $event->id)->get();
        return view('franchise_staff.event.view' , compact('event','eventItems'));
    }


    public function flavors(){

        $deliveredOrders = FpgOrder::where('status', 'delivered')->get();
        $shippedOrders = FpgOrder::where('status', 'shipped')->count();
        $paidOrders = FpgOrder::where('status', 'paid')->count();
        $pendingOrders = FpgOrder::where('status', 'pending')->count();

        $orders = FpgOrder::where('user_ID' , Auth::user()->franchisee_id)->get();

        $totalOrders = $orders->count();

        return view('franchise_staff.flavors.index', compact('deliveredOrders', 'shippedOrders', 'pendingOrders','paidOrders', 'orders', 'totalOrders'));
    }


    public function index() {
        $data['customers'] = Customer::where('franchisee_id' , Auth::user()->franchisee_id)->get();
        $data['customerCount'] = Customer::where('franchisee_id' , Auth::user()->franchisee_id)->count();
        return view('franchise_staff.customer.index' ,$data);
    }

    public function create() {
        return view('franchise_staff.customer.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'required|numeric|digits_between:8,16',
            'email' => 'required|email|max:191',
    'state' => 'required|alpha|size:2', // 2-letter state code (alphabetic)
    'zip_code' => 'required|digits:5', // 5 digits zip code

            'address1' => 'required|max:191',
            'address2' => 'nullable|max:191',
        ]);

        $customer = Customer::create([
            'franchisee_id' => Auth::user()->franchisee_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address1' => $request->address1,
            'address2' => $request->address2,
        ]);


        return redirect()->route('franchise_staff.customer')->with('success' , 'Customer created successfully');
    }

    public function edit($id) {
        $data['customer'] = Customer::where('customer_id' , $id)->firstorfail();
        return view('franchise_staff.customer.edit' , $data);
    }

    public function update(Request $request , $id){
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'required|numeric|digits_between:8,16',
            'email' => 'required|email|max:191',
    'zip_code' => 'required|digits:5', // 5 digits zip code
    'state' => 'required|alpha|size:2', // 2-letter state code (alphabetic)

            'address1' => 'required|max:191',
            'address2' => 'nullable|max:191',
        ]);

        $customer = Customer::where('customer_id' , $id)->update([
            'franchisee_id' => Auth::user()->franchisee_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address1' => $request->address1,
            'address2' => $request->address2,
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
        ->join('fpg_items as fi', 'od.fgp_item_id', '=', 'fi.fgp_item_id')
        ->where('od.fpg_order_id', $orderId)
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
