<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FgpOrder;
use App\Models\FgpItem;
use App\Models\Franchisee;
use App\Models\AdditionalCharge;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Customer;

class ViewOrdersController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $orders = FgpOrder::query()
                ->with(['user', 'customer'])
                ->select('fgp_orders.*');

            return DataTables::of($orders)
                ->addColumn('order_number', function ($order) {
                    return '<a href="' . route('corporate_admin.vieworders.edit', ['orderId' => $order->fgp_ordersID]) . '" class="text-primary fs-12">' . 
                           $order->getOrderNum() . '</a>';
                })
                ->addColumn('date_time', function ($order) {
                    return Carbon::parse($order->date_transaction)->format('M d, Y h:i A');
                })
                ->addColumn('total_amount', function ($order) {
                    $totalAmount = DB::table('fgp_order_details')
                        ->where('fgp_order_id', $order->fgp_ordersID)
                        ->selectRaw('SUM(unit_number * unit_cost) as total')
                        ->value('total');
                    return '$' . number_format($totalAmount, 2);
                })
                ->addColumn('ordered_by', function ($order) {
                    $franchisee = Franchisee::where('franchisee_id', $order->user_ID)->first();
                    $customer = Customer::where('customer_id', $order->customer_id)->first();
                    
                    if ($customer) {
                        return '<a href="' . route('corporate_admin.customer.view', ['id' => $customer->customer_id]) . '" class="text-primary">' . 
                               $customer->name . '</a>';
                    } elseif ($franchisee) {
                        return '<a href="' . route('profile.show', ['profile' => $franchisee->franchisee_id]) . '" class="text-primary">' . 
                               $franchisee->business_name . '</a>';
                    }
                    return 'Unknown';
                })
                ->addColumn('shipping_address', function ($order) {
                    return $order->fullShippingAddress();
                })
                ->addColumn('items_count', function ($order) {
                    return '<span class="cursor-pointer text-primary order-detail-trigger" data-id="' . $order->fgp_ordersID . '">' .
                           DB::table('fgp_order_details')->where('fgp_order_id', $order->fgp_ordersID)->count() . ' items</span>';
                })
                ->addColumn('issues', function ($order) {
                    return $order->orderDiscrepancies->count() > 0 
                        ? '<span class="badge bg-danger text-white">Alert</span>'
                        : '<span class="badge bg-success text-white">OK</span>';
                })
                ->addColumn('status', function ($order) {
                    $statuses = ['Pending', 'Paid', 'Shipped', 'Delivered'];
                    $select = '<select class="status-select" data-date="' . $order->date_transaction . '" data-fgp-orders-id="' . $order->fgp_ordersID . '" tabindex="null">';
                    foreach ($statuses as $status) {
                        $selected = $order->status == $status ? 'selected' : '';
                        $select .= '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                    }
                    $select .= '</select>';
                    return $select;
                })
                ->rawColumns(['order_number', 'ordered_by', 'items_count', 'issues', 'status'])
                ->make(true);
        }

        $totalOrders = FgpOrder::count();
        return view('corporate_admin.view_orders.index', compact('totalOrders'));
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

            Log::info('Received Order Data:', ['ordered_items' => $items]);

            if (empty($items)) {
                Log::warning('No items received in order confirmation.');
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
            Log::error('Error in confirmOrder method: ' . $e->getMessage());
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
