<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FgpOrder;
use App\Models\User;
use App\Models\FgpItem;
use App\Models\Franchisee;
use App\Models\AdditionalCharge;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Log;

class ViewOrdersController extends Controller
{
    public function index()
    {
        $deliveredOrders = FgpOrder::where('status', 'delivered')->get();
        $shippedOrders = FgpOrder::where('status', 'shipped')->count();
        $paidOrders = FgpOrder::where('status', 'paid')->count();
        $pendingOrders = FgpOrder::where('status', 'pending')->count();


        // $orders = FgpOrder::where('user_ID', Auth::id())
        // ->select(
        //     'user_ID',
        //     'date_transaction',
        //     \DB::raw('SUM(unit_number) as total_quantity'),
        //     \DB::raw('SUM(unit_number * unit_cost) as total_amount'),
        //     'status'
        // )
        // ->groupBy('date_transaction', 'user_ID', 'status')
        // ->orderBy('date_transaction', 'desc')
        // ->with('user') // Eager load user information
        // ->get()
        // ->map(function ($order) {
        //     $order->date_transaction = Carbon::parse($order->date_transaction);
        //     return $order;
        // });

        $orders = FgpOrder::get();

        $totalOrders = $orders->count();

        return view('corporate_admin.view_orders.index', compact('deliveredOrders', 'shippedOrders', 'pendingOrders', 'paidOrders', 'orders', 'totalOrders'));
    }

    public function viewordersDetail(Request $request)
    {
        $orderId = $request->input('id');

        $orderDetails = DB::table('fgp_order_details as od')
        ->join('fgp_items as fi', 'od.fgp_item_id', '=', 'fi.fgp_item_id')
        ->where('od.fgp_order_id', $orderId)
        ->select('od.*', 'fi.name')
        ->get();

    // Format the date_transaction for each order detail
    foreach ($orderDetails as $detail) {
        // Format the date using Carbon
        $detail->formatted_date = Carbon::parse($detail->date_transaction)->format('M d, Y h:i A');
    }


        return response()->json([
            'orderDetails' => $orderDetails,
        ]);
    }

    public function updateStatus(Request $request)
    {
        // try {
        //     // Log the raw request data for debugging
        //     \Log::info('Update status request data:', $request->all());

        //     $validated = $request->validate([
        //         'status' => 'required|string|in:Pending,Paid,Shipped,Delivered',
        //         'date_transaction' => 'required|string',
        //         'fgp_ordersID' => 'required|exists:fgp_orders,fgp_ordersID' // Validate fgp_ordersID
        //     ]);

        //     // Log that validation passed
        //     \Log::info('Validation passed, proceeding with update');

        //     // Query by fgp_ordersID
        //     $order = DB::table('fgp_orders')->where('fgp_ordersID', $request->fgp_ordersID)->firstOrFail();

        //     $order->status = $request->status;
        //     $order->save();

        //     \Log::info('Order updated successfully, fgp_ordersID: ' . $request->fgp_ordersID);

        //     return response()->json([
        //         'message' => 'Order status updated successfully!'
        //     ]);
        // } catch (ValidationException $e) {
        //     // Log validation errors
        //     \Log::error('Validation failed:', $e->errors());

        //     return response()->json([
        //         'message' => 'Validation failed',
        //         'errors' => $e->errors()
        //     ], 422);
        // } catch (\Exception $e) {
        //     // Log other errors
        //     \Log::error('Exception in updateStatus: ' . $e->getMessage());

        //     return response()->json([
        //         'message' => $e->getMessage()
        //     ], 500);
        // }
            $order = DB::table('fgp_orders')->where('fgp_ordersID', $request->fgp_ordersID)->firstOrFail();
            if($order){
                $updated = DB::table('fgp_orders')
                    ->where('fgp_ordersID', $request->fgp_ordersID)
                    ->update(['status' => $request->status]);
                return response()->json([
                    'message' => 'Order status updated successfully!'
                ]);
            }else{
                return response()->json([
                    'error' => '404 no found'
                ]);
            }
    }

    public function edit($orderId)
    {
        $order = FgpOrder::find($orderId);
        $currentMonth = strval(Carbon::now()->format('n'));

        // Fetch only orderable, in-stock, and currently available items
        $pops = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0) // Ensure item is in stock
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

        $categorizedItems = [];
        foreach ($pops as $pop) {
            foreach ($pop->categories as $category) {
                $types = json_decode($category->type, true); // Decode JSON types

                foreach ($types as $type) {
                    $categorizedItems[$type][$category->name][] = $pop;
                }
            }
        }
        return view('corporate_admin.view_orders.edit', compact('order', 'categorizedItems'));
    }


    public function orderposps(){
        $currentMonth = strval(Carbon::now()->format('n')); // Get current month as single-digit (1-12)

        // Fetch only items that are orderable, in stock, and available in the current month
        $pops = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0) // Ensure the item is in stock
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

        // Count total available flavor pops
        $totalPops = $pops->count();

        return view('corporate_admin.orderpops.index', compact('pops', 'totalPops'));
    }


    public function confirmPage(){
        $items = session('ordered_items', []);

        if (empty($items)) {
            return redirect()->route('franchise.orderpops.index')->withErrors('No items selected.');
        }

        $requiredCharges = AdditionalCharge::where('charge_optional', 'required')->where('status', 1)->get();
        $optionalCharges = AdditionalCharge::where('charge_optional', 'optional')->where('status', 1)->get();
        $users = Franchisee::get();
        return view('corporate_admin.orderpops.confirm', compact('items', 'requiredCharges', 'optionalCharges','users'));
    }

    public function confirmOrder(Request $request)
    {
        try {
            $items = $request->input('ordered_items');

            \Log::info('Received Order Data:', ['ordered_items' => $items]);

            if (empty($items)) {
                \Log::warning('No items received in order confirmation.');
                return response()->json(['error' => 'No items selected for order.'], 400);
            }

            // Convert price strings to numeric values for calculations
            foreach ($items as &$item) {
                $item['price'] = floatval(str_replace(['$', ','], '', $item['price']));
                $item['quantity'] = $item['quantity'] ?? 1; // Set default quantity if not provided
            }

            // Store items in the session for retrieval on the confirmation page
            session(['ordered_items' => $items]);

            return response()->json(['redirect' => route('corporate_admin.confirm.page')]);
        } catch (\Exception $e) {
            \Log::error('Error in confirmOrder method: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }


    public function store(Request $request)
    {
        $minCases = 12; // Can be made configurable via settings
        $factorCase = 3; // Can be made configurable via settings
        // Validate request
        $validated = $request->validate([
            'user_ID' => 'required',
            'items' => 'required|array',
            'items.*.fgp_item_id' => 'required|exists:fgp_items,fgp_item_id',
            'items.*.user_ID' => 'required|exists:users,user_id',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.unit_number' => 'required|integer|min:1', // Allow any positive integer
        ]);

        // Calculate total case quantity
        $totalCaseQty = collect($validated['items'])->sum('unit_number');

        // Check if the total order quantity meets the minimum required cases
        if ($totalCaseQty < $minCases) {
            return redirect()->back()->withErrors(['Order must have at least ' . $minCases . ' cases.']);
        }

        // Check if the total order quantity is a valid multiple of the factor case
        if ($totalCaseQty % $factorCase !== 0) {
            return redirect()->back()->withErrors(['Order quantity must be a multiple of ' . $factorCase . '.']);
        }

        $orders = FgpOrder::create([
            'user_ID' => $request->user_ID,
            'customer_id' => $request->customer_id,
            'date_transaction' => now(),
            'status' => 'Pending',
        ]);

        foreach ($validated['items'] as $item) {
            DB::table('fgp_order_details')->insert([
                'fgp_order_id' => $orders->id,
                'fgp_item_id' => $item['fgp_item_id'],
                'unit_cost' => $item['unit_cost'],
                'unit_number' => $item['unit_number'],
                'date_transaction' => now(),
                'ACH_data' => null,
            ]);
        }

        return redirect()->route('corporate_admin.vieworders.index')->with('success', 'Order placed successfully!');
    }
}
