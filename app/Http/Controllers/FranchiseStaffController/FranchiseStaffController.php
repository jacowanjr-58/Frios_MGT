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
use DB;
use Illuminate\Support\Facades\Auth;

class FranchiseStaffController extends Controller
{
    public function dashboard()
    {
        return view('franchise_staff.dashboard');
    }

    public function calendar(){
        $events = Event::where('franchisee_id' , Auth::user()->franchisee_id)->get();
        $badgeEvents = Event::where('franchisee_id' , Auth::user()->franchisee_id)->orderBy('created_at', 'DESC')
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
                $query->where('franchisee_id', Auth::user()->franchisee_id);
            })
            ->get();
        return view('franchise_staff.event.report', compact('eventItems'));
    }

    public function eventView($id){
        $event = Event::where('id' , $id)->firstorfail();
        $eventItems = FranchiseEventItem::where('event_id' , $event->id)->get();
        return view('franchise_staff.event.view' , compact('event','eventItems'));
    }


    public function flavors($franchisee_id){

        $deliveredOrders = FgpOrder::where('status', 'delivered')->where('franchisee_id' , $franchisee_id)->get();
       
        $shippedOrders = FgpOrder::where('status', 'shipped')->where('franchisee_id' , $franchisee_id)->count();
        $paidOrders = FgpOrder::where('status', 'paid')->where('franchisee_id' , $franchisee_id)->count();
        $pendingOrders = FgpOrder::where('status', 'pending')->where('franchisee_id' , $franchisee_id)->count();

        $orders = FgpOrder::where('franchisee_id' , $franchisee_id)->get();

        $totalOrders = $orders->count();
       

        return view('franchise_staff.flavors.index', compact('deliveredOrders', 'shippedOrders', 'pendingOrders','paidOrders', 'orders', 'totalOrders','franchisee_id'));
    }


    public function index() {
       
        $data['customers'] = Customer::where('franchisee_id' , Auth::user()->franchisee_id)->get();
        $data['customerCount'] = Customer::where('franchisee_id' , Auth::user()->franchisee_id)->count();
        return view('franchise_staff.customer.index' ,$data);
    }

    public function create( $franchisee ) {
        return view('franchise_staff.customer.create', compact('franchisee'));
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
            'franchisee_id' => Auth::user()->franchisee_id,
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
        $data['customer'] = Customer::where('customer_id' , $id)->firstorfail();
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

        $customer = Customer::where('customer_id' , $id)->update([
            'franchisee_id' => Auth::user()->franchisee_id,
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
        ->join('fgp_items as fi', 'od.fgp_item_id', '=', 'fi.fgp_item_id')
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
