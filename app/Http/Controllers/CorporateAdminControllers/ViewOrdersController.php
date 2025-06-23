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
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Customer;

class ViewOrdersController extends Controller
{
    public function index($franchiseeId)
    {
        if (request()->ajax()) {
            $orders = FgpOrder::query()
            ->where('franchisee_id', $franchiseeId)
                ->with(['user', 'customer']);

            return DataTables::of($orders)
                ->addColumn('order_number', function ($order) {
                    return '<a href="' . route('vieworders.edit', ['orderId' => $order->fgp_ordersID]) . '" class="text-primary fs-12">' .
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
                        return '<a href="' . route('customer.view', ['id' => $customer->customer_id]) . '" class="text-primary">' .
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
                    
                    // Check permission for status updates
                    if (Auth::check() && Auth::user()->can('franchise_orders.edit')) {
                    $select = '<select class="status-select" data-date="' . $order->date_transaction . '" data-fgp-orders-id="' . $order->fgp_ordersID . '" tabindex="null">';
                    foreach ($statuses as $status) {
                        $selected = $order->status == $status ? 'selected' : '';
                        $select .= '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                    }
                    $select .= '</select>';
                    return $select;
                    } else {
                        // Show status as read-only badge
                        return '<span class="badge bg-secondary">' . ($order->status ?? 'Unknown') . '</span>';
                    }
                })
                ->addColumn('ups_label', function ($order) {
                    if (Auth::check() && Auth::user()->can('franchise_orders.edit')) {
                    if ($order->status != 'Shipped') {
                        return '<a href="' . url('/order/' . $order->fgp_ordersID . '/create-ups-label') . '" class="btn btn-primary btn-sm" target="_blank">Generate UPS Label</a>';
                    }
                    return '<span class="text-muted">Add Label and Tracking</span>';
                    } else {
                        return '<span class="text-muted">No Access</span>';
                    }
                })
                ->addColumn('action', function ($order) {
                    $actions = '<div class="dropdown">';
                    $actions .= '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">';
                    $actions .= '<i class="fa fa-cog"></i>';
                    $actions .= '</button>';
                    $actions .= '<ul class="dropdown-menu">';
                    
                    // View Details - check permission
                    if (Auth::check() && Auth::user()->can('franchise_orders.view')) {
                        $actions .= '<li><a class="dropdown-item" href="javascript:void(0)" onclick="viewOrderDetails(' . $order->fgp_ordersID . ')"><i class="fa fa-eye me-2"></i>View Details</a></li>';
                    }
                    
                    // Edit - check permission
                    if (Auth::check() && Auth::user()->can('franchise_orders.edit')) {
                        $actions .= '<li><a class="dropdown-item" href="' . route('vieworders.edit', ['orderId' => $order->fgp_ordersID]) . '"><i class="fa fa-edit me-2"></i>Edit</a></li>';
                    }
                    
                    // Divider - only show if there are actions above and below
                    if ((Auth::check() && Auth::user()->can('franchise_orders.view')) || (Auth::check() && Auth::user()->can('franchise_orders.edit'))) {
                        if (Auth::check() && Auth::user()->can('franchise_orders.edit')) {
                            $actions .= '<li><hr class="dropdown-divider"></li>';
                        }
                    }
                    
                    // Cancel Order - check permission
                    if (Auth::check() && Auth::user()->can('franchise_orders.edit')) {
                        $actions .= '<li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="changeOrderStatus(' . $order->fgp_ordersID . ', \'cancelled\')"><i class="fa fa-times me-2"></i>Cancel Order</a></li>';
                    }
                    
                    // If no permissions, show view-only message
                    if (!Auth::check() || (!Auth::user()->can('franchise_orders.view') && !Auth::user()->can('franchise_orders.edit'))) {
                        $actions .= '<li><span class="dropdown-item text-muted">No Actions Available</span></li>';
                    }
                    
                    $actions .= '</ul>';
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['order_number', 'ordered_by', 'items_count', 'issues', 'status', 'ups_label', 'action'])
                ->make(true);
        }

        $totalOrders = FgpOrder::count();
        return view('corporate_admin.view_orders.index', compact('totalOrders', 'franchiseeId'));
    }

    public function viewordersDetail(Request $request)
    {
        $orderId = $request->input('id');

        $orderDetails = DB::table('fgp_order_details as od')
        ->join('fgp_items as fi', 'od.fgp_item_id', '=', 'fi.fgp_item_id')
        ->where('od.fgp_order_id', $orderId)
        ->select('od.*', 'fi.name')
        ->get();

        // Get the main order information
        $order = FgpOrder::find($orderId);

        // Format the date_transaction for each order detail
        foreach ($orderDetails as $detail) {
            // Format the date using Carbon
            $detail->formatted_date = Carbon::parse($detail->date_transaction)->format('M d, Y h:i A');
        }

        // Return HTML view instead of JSON
        return view('corporate_admin.view_orders.detail_modal', compact('orderDetails', 'order'))->render();
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
        $order = FgpOrder::with('orderDetails','user')->find($orderId);
        $currentMonth = strval(Carbon::now()->format('n'));

        // Fetch only orderable, in-stock, and currently available items
        $allItems = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0) // Ensure item is in stock
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

    return view('corporate_admin.view_orders.edit', compact('order', 'allItems'));
    }


public function update(Request $request, $orderId)
{
    $order = FgpOrder::with('orderDetails')->findOrFail($orderId);

    // Validate shipping and items
    $validated = $request->validate([
        'ship_to_name' => 'required|string|max:255',
        'ship_to_address1' => 'required|string|max:255',
        'ship_to_address2' => 'nullable|string|max:255',
        'ship_to_city' => 'required|string|max:255',
        'ship_to_state' => 'required|string|max:255',
        'ship_to_zip' => 'required|string|max:20',
        'items' => 'required|array',
        'items.*.id' => 'nullable|integer|exists:fgp_order_details,id',
        'items.*.fgp_item_id' => 'required|exists:fgp_items,fgp_item_id',
        'items.*.unit_cost' => 'required|numeric|min:0',
        'items.*.unit_number' => 'required|integer|min:1',
    ]);

    // Update shipping info
    $order->update([
        'ship_to_name' => $validated['ship_to_name'],
        'ship_to_address1' => $validated['ship_to_address1'],
        'ship_to_address2' => $validated['ship_to_address2'],
        'ship_to_city' => $validated['ship_to_city'],
        'ship_to_state' => $validated['ship_to_state'],
        'ship_to_zip' => $validated['ship_to_zip'],
    ]);

    // Gather submitted detail IDs
    $submittedDetailIds = collect($validated['items'])
        ->pluck('id')
        ->filter()
        ->map(fn($id) => (int)$id)
        ->toArray();

    // Remove deleted items
    $order->orderDetails()
        ->whereNotIn('id', $submittedDetailIds)
        ->delete();

    // Update or create items
    foreach ($validated['items'] as $item) {
        if (!empty($item['id'])) {
            // Update existing
            $order->orderDetails()
                ->where('id', $item['id'])
                ->update([
                    'unit_number' => $item['unit_number'],
                    'unit_cost' => $item['unit_cost'],
                    'fgp_item_id' => $item['fgp_item_id'],
                    'date_transaction' => now(),
                ]);
        } else {
            // Add new
            $order->orderDetails()->create([
                'fgp_item_id' => $item['fgp_item_id'],
                'fgp_order_id' => $order->fgp_ordersID,
                'unit_number' => $item['unit_number'],
                'unit_cost' => $item['unit_cost'],
                'date_transaction' => now(),
            ]);
        }
    }

    return redirect()
    ->route('vieworders.index')
    ->with('success', 'Order #' . $order->getOrderNum() . ' updated successfully!');
}




    public function orderposps($franchisee)
    {
        if (request()->ajax()) {
            $currentMonth = strval(Carbon::now()->format('n'));
            $franchiseeID = session('franchisee_id');
            $pops = FgpItem::where('franchisee_id', $franchiseeID)
                ->with('categories')
                ->where('orderable', 1)
                ->where('internal_inventory', '>', 0)
                ->whereJsonContains('dates_available', $currentMonth);

            return DataTables::of($pops)
                ->addColumn('checkbox', function ($pop) {
                    if (Auth::check() && Auth::user()->can('franchise_orders.create')) {
                        return '<div class="form-check checkbox-secondary">
                            <input class="form-check-input pop-checkbox" type="checkbox" value="' . $pop->fgp_item_id . '" id="flexCheckDefault' . $pop->fgp_item_id . '">
                            <label class="form-check-label" for="flexCheckDefault' . $pop->fgp_item_id . '"></label>
                        </div>';
                    } else {
                        return '<div class="form-check checkbox-secondary">
                            <input class="form-check-input pop-checkbox" type="checkbox" value="' . $pop->fgp_item_id . '" id="flexCheckDefault' . $pop->fgp_item_id . '" disabled title="You don\'t have permission to create orders">
                            <label class="form-check-label" for="flexCheckDefault' . $pop->fgp_item_id . '"></label>
                        </div>';
                    }
                })
                ->addColumn('image', function ($pop) {
                    if ($pop->image1) {
                        return '<img src="' . asset('storage/' . $pop->image1) . '" alt="Image" style="width: 50px; height: 50px; object-fit: contain;">';
                    }
                    return '<span>No Image</span>';
                })
                ->addColumn('price', function ($pop) {
                    return '$' . number_format($pop->case_cost, 2);
                })
                ->addColumn('categories', function ($pop) {
                    $formattedCategories = '';
                    if($pop->categories->isNotEmpty()) {
                            foreach($pop->categories as $category) {
                                $formattedCategories .= '<span class="badge bg-primary me-2 mb-1">'.$category->name.'</span>';
                            }
                        } else {
                            $formattedCategories = 'No Category';
                        }
                    return $formattedCategories;
                })
                ->filterColumn('categories', function ($query, $keyword) {
                    $query->whereHas('categories', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
                })
                ->addColumn('stock_status', function ($pop) {
                    return '<span class="badge bg-success">In Stock</span>';
                })
                ->addColumn('availability', function ($pop) {
                    return '<span class="badge bg-success">Available</span>';
                })
                ->rawColumns(['checkbox', 'image', 'categories', 'stock_status', 'availability'])
                ->make(true);
        }

        $currentMonth = strval(Carbon::now()->format('n'));
        $totalPops = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0)
            ->whereJsonContains('dates_available', $currentMonth)
            ->count();

        $franchiseeId = $franchisee;

        return view('corporate_admin.orderpops.index', compact('totalPops', 'franchiseeId'));
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

            return response()->json(['redirect' => route('confirm.page')]);
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

        return redirect()->route('vieworders.index')->with('success', 'Order placed successfully!');
    }
}
