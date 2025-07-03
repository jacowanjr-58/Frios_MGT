<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FgpOrder;
use App\Models\FgpItem;
use App\Models\Franchise;
use App\Models\AdditionalCharge;
use App\Models\UpsShipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Customer;

class ViewOrdersController extends Controller
{
    public function index($franchise)
    {
        if (request()->ajax()) {
            $orders = FgpOrder::query()
                ->with(['user', 'franchise', 'orderItems.item'])    
                ->select('fgp_orders.*')
                ->when($franchise !== 'all', function ($query) use ($franchise) {
                    $query->whereHas('franchise', function ($query) use ($franchise) {
                        $query->where('franchise_id', $franchise);
                    });
                })
                ->whereNull('deleted_at'); // Exclude soft deleted orders

            // Apply filters

            if (request()->filled('status')) {
                $orders->where('is_paid', request('status'));
            }

            if (request()->filled('shipping_address')) {
                $orders->where(function($q) {
                    $address = request('shipping_address');
                    $q->whereRaw("TRIM(CONCAT(COALESCE(ship_to_address1, ''), ' ', COALESCE(ship_to_address2, ''), ', ', COALESCE(ship_to_city, ''), ', ', COALESCE(ship_to_state, ''), ' ', COALESCE(ship_to_zip, ''))) LIKE ?", ['%' . $address . '%']);
                });
            }

            if (request()->filled('flavor')) {
                $orders->whereHas('orderItems', function($query) {
                    $query->where('fgp_item_id', request('flavor'));
                });
            }

            if (request()->filled('date_from')) {
                $orders->whereDate('created_at', '>=', request('date_from'));
            }

            if (request()->filled('date_to')) {
                $orders->whereDate('created_at', '<=', request('date_to'));
            }

            return DataTables::of($orders)
                ->addColumn('order_number', function ($order) use ($franchise) {
                    return '<a href="' . route('franchise.orders.edit', ['franchise' => $franchise, 'orders' => $order->id]) . '" class="text-primary fs-12">' .
                           $order->getOrderNum() . '</a>';
                })
                ->addColumn('date_time', function ($order) {
                    return $order->created_at; 
                })
                ->addColumn('total_amount', function ($order) {
                    $totalAmount = DB::table('fgp_order_items')
                        ->where('fgp_order_id', $order->id) 
                        ->selectRaw('SUM(quantity * unit_price) as total') //sum of unit_number * unit_cost
                        ->value('total');
                    return '$' . number_format($totalAmount, 2);
                })

                 ->addColumn('ordered_by', function ($order) use ($franchise) {
                    if ($order->franchise) {
                        return '<span class="text-primary">' . $order->user->name . '</span>';
                    }
                    return 'Unknown';
                })
                ->addColumn('franchise', function ($order) {
                    if ($order->franchise) {
                        return '<strong>' . $order->franchise->business_name . '</strong><br>';
                    }
                    return '<span class="text-muted">No Franchise</span>';
                })
                ->addColumn('flavors', function ($order) {
                    if ($order->orderItems->count() > 0) {
                        $flavorList = $order->orderItems->map(function ($item) {
                            $flavorName = $item->item->name ?? 'Unknown Flavor';
                            return "({$item->unit_number}) {$flavorName}";
                        })->implode('<br>');
                        return '<div class="small">' . $flavorList . '</div>';
                    }
                    return '<span class="text-muted">No Items</span>';
                })
                ->addColumn('shipping_address', function ($order) {
                    return $order->fullShippingAddress();
                })
                ->addColumn('items_count', function ($order) {
                    return '<span class="cursor-pointer text-primary order-detail-trigger" data-id="' . $order->id . '">' .
                            DB::table('fgp_order_items')->where('fgp_order_id', $order->id)->count() . ' items</span>';
                })
                ->addColumn('issues', function ($order) {
                    return $order->orderItems->count() > 0
                        ? '<span class="badge bg-danger text-white">Alert</span>'
                        : '<span class="badge bg-success text-white">OK</span>';
                })
                ->addColumn('status', function ($order) {
                    return $order->is_paid 
                        ? '<span class="badge bg-success text-white">Paid</span>'
                        : '<span class="badge bg-warning text-white">Pending</span>';
                })

                ->addColumn('ups_label', function ($order) {
                    if (Auth::check() && Auth::user()->can('orders.edit')) {
                        return '<a href="' . url('/order/' . $order->id . '/create-ups-label') . '" class="btn btn-primary btn-sm" target="_blank">Generate UPS Label</a>';
                    } else {
                        return '<span class="text-muted">No Access</span>';
                    }
                })
                ->addColumn('action', function ($order) use ($franchise) {
                    $actions = '<div class="dropdown">';
                    $actions .= '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">';
                    $actions .= '<i class="fa fa-cog"></i>';
                    $actions .= '</button>';
                    $actions .= '<ul class="dropdown-menu">';

                    // View Details - check permission
                    if (Auth::check() && Auth::user()->can('orders.view')) {
                        $actions .= '<li><a class="dropdown-item" href="javascript:void(0)" onclick="viewOrderDetails(' . $order->id . ')"><i class="fa fa-eye me-2"></i>View Details</a></li>';
                    }

                    // Edit - check permission
                    if (Auth::check() && Auth::user()->can('orders.edit')) {
                        $actions .= '<li><a class="dropdown-item" href="' . route('franchise.orders.edit', ['franchise' => $franchise, 'orders' => $order->id]) . '"><i class="fa fa-edit me-2"></i>Edit</a></li>';
                    }

                    // Divider - only show if there are actions above and below
                    if ((Auth::check() && Auth::user()->can('orders.view')) || (Auth::check() && Auth::user()->can('orders.edit'))) {
                        if (Auth::check() && Auth::user()->can('orders.edit')) {
                            $actions .= '<li><hr class="dropdown-divider"></li>';
                        }
                    }

                    // Cancel Order - check permission
                    if (Auth::check() && Auth::user()->can('orders.edit')) {
                        $actions .= '<li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="changeOrderStatus(' . $order->id . ', \'cancelled\')"><i class="fa fa-times me-2"></i>Cancel Order</a></li>';
                    }

                    // If no permissions, show view-only message
                    if (!Auth::check() || (!Auth::user()->can('orders.view') && !Auth::user()->can('orders.edit'))) {
                        $actions .= '<li><span class="dropdown-item text-muted">No Actions Available</span></li>';
                    }

                    $actions .= '</ul>';
                    $actions .= '</div>';

                    return $actions;
                })
                ->rawColumns(['order_number', 'ordered_by', 'franchise', 'flavors', 'items_count', 'issues', 'status', 'ups_label', 'action'])
                ->make(true);
        }

        $totalOrders = FgpOrder::whereNull('deleted_at')->count(); // Exclude soft deleted orders from count
        $franchiseId = $franchise;
        return view('corporate_admin.orders.index', compact('totalOrders', 'franchiseId'));
    }

    
    public function getFlavors($franchiseId)
    {
        try {
            $flavors = DB::table('fgp_order_items')
                ->join('fgp_orders', 'fgp_order_items.fgp_order_id', '=', 'fgp_orders.id')
                ->join('fgp_items', 'fgp_order_items.fgp_item_id', '=', 'fgp_items.id')
                ->where('fgp_orders.franchise_id', $franchiseId)
                ->select('fgp_items.id as fgp_item_id', 'fgp_items.name')
                ->distinct()
                ->orderBy('fgp_items.name')
                ->get();

            return response()->json([
                'success' => true,
                'flavors' => $flavors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading flavors: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getShippingAddresses($franchiseId)
    {
        try {
            $addresses = FgpOrder::where('franchise_id', $franchiseId)
                ->whereNotNull('ship_to_address1')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'address' => $order->fullShippingAddress()
                    ];
                })
                ->unique('address')
                ->values()
                ->sortBy('address');

            return response()->json([
                'success' => true,
                'addresses' => $addresses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading shipping addresses: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ordersDetail(Request $request, $franchiseId)
    {
        $orderId = $request->input('id');
        $franchiseId = intval($franchiseId);
        $orderDetails = DB::table('fgp_order_items as od')
                    ->join('fgp_items as fi', 'od.fgp_item_id', '=', 'fi.id')
        ->where('od.fgp_order_id', $orderId)
        ->select('od.*', 'fi.name')
        ->get();

        // Get the main order information
        $order = FgpOrder::find($orderId);

        // Format the date for each order detail
        foreach ($orderDetails as $detail) {
            // Format the date using Carbon - handle both date_transaction and created_at
            $dateField = $detail->date_transaction ?? $detail->created_at ?? $detail->updated_at;
            if ($dateField) {
                $detail->formatted_date = Carbon::parse($dateField)->format('M d, Y h:i A');
            } else {
                $detail->formatted_date = 'N/A';
            }
        }

        // Return HTML view instead of JSON
        return view('corporate_admin.orders.detail_modal', compact('orderDetails', 'order', 'franchiseId'))->render();
    }



    public function create($franchiseId)
    {
        $franchiseId = intval($franchiseId);
        $currentMonth = strval(Carbon::now()->format('n'));

        // Get the franchise
        $franchise = Franchise::findOrFail($franchiseId);
        
        // Get all franchises if user is corporate_admin
        $allFranchises = collect();
        if (Auth::check() && Auth::user()->role == 'corporate_admin') {
            $allFranchises = Franchise::orderBy('business_name')->get();
        }
        
        // Fetch only orderable, in-stock, and currently available items
        $allItems = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0)
            ->get()
            ->filter(function ($item) use ($currentMonth) {
                $availableMonths = is_array($item->dates_available)
                    ? $item->dates_available
                    : json_decode($item->dates_available, true);
        
                return in_array($currentMonth, $availableMonths ?? []);
            });
        
        return view('corporate_admin.orders.create', compact('franchiseId', 'franchise', 'allItems', 'allFranchises'));
    }


    public function edit($franchiseId, $orderId)
    {
        $order = FgpOrder::with('orderItems.item','user')->find($orderId);
        $currentMonth = strval(Carbon::now()->format('n'));
        // Get the franchise
        $franchise = null;
        $franchise = Franchise::when($franchiseId != "all", function ($query) use ($franchiseId) {
            return $query->where('id', $franchiseId);
        })->first();
        // Get all franchises if user is corporate_admin
        $allFranchises = collect();
        if (Auth::check() && Auth::user()->role == 'corporate_admin') {
            $allFranchises = Franchise::orderBy('business_name')->get();
        }

        // Fetch only orderable, in-stock, and currently available items
        $allItems = FgpItem::where('orderable', 1)
        ->where('internal_inventory', '>', 0)
        ->get()
        ->filter(function ($pop) use ($currentMonth) {
            $availableMonths = is_array($pop->dates_available)
                ? $pop->dates_available
                : json_decode($pop->dates_available, true);
    
            return in_array($currentMonth, $availableMonths ?? []);
        });
    
        return view('corporate_admin.orders.edit', compact('order', 'allItems', 'franchiseId', 'franchise', 'allFranchises'));
    }


public function update(Request $request, $franchiseId, $orderId)
{
    // If corporate_admin selected a franchise from dropdown, use that instead of route parameter
    if ($request->filled('franchise_id') && Auth::check() && Auth::user()->role == 'corporate_admin') {
        $franchiseId = intval($request->input('franchise_id'));
    } else {
        $franchiseId = intval($franchiseId);
    }
    
    $order = FgpOrder::with('orderItems')->findOrFail($orderId);

    $minCases = 12; // Can be made configurable via settings
    $factorCase = 3; // Can be made configurable via settings

    // Validate shipping and items
    $validated = $request->validate([
        'franchise_id' => Auth::check() && Auth::user()->role == 'corporate_admin' ? 'required|exists:franchises,id' : 'nullable|exists:franchises,id',
        'ship_to_name' => 'nullable|string|max:255',
        'ship_to_address1' => 'nullable|string|max:255',
        'ship_to_address2' => 'nullable|string|max:255',
        'ship_to_city' => 'nullable|string|max:255',
        'ship_to_state' => 'nullable|string|max:255',
        'ship_to_zip' => 'nullable|string|max:20',
        'ship_to_phone' => 'nullable|string|max:50',
        'items' => 'required|array',
        'items.*.id' => 'nullable|integer|exists:fgp_order_items,id',
        'items.*.fgp_item_id' => 'required|exists:fgp_items,id',
        'items.*.unit_cost' => 'required|numeric|min:0',
        'items.*.unit_number' => 'required|integer|min:1',
    ]);

    // Calculate total case quantity
    $totalCaseQty = collect($validated['items'])->sum('unit_number');

    // Check if the total order quantity is a valid multiple of the factor case
    // if ($totalCaseQty % $factorCase !== 0) {
    //     return redirect()->back()->withErrors(['Order quantity must be a multiple of ' . $factorCase . '.']);
    // }

    // Calculate total amount
    $totalAmount = collect($validated['items'])->sum(function ($item) {
        return $item['unit_cost'] * $item['unit_number'];
    });

    // Update order info
    $order->update([
        'franchise_id' => $franchiseId,
        'amount' => $totalAmount,
        'ship_to_name' => $validated['ship_to_name'] ?? null,
        'ship_to_address1' => $validated['ship_to_address1'] ?? null,
        'ship_to_address2' => $validated['ship_to_address2'] ?? null,
        'ship_to_city' => $validated['ship_to_city'] ?? null,
        'ship_to_state' => $validated['ship_to_state'] ?? null,
        'ship_to_zip' => $validated['ship_to_zip'] ?? null,
        'ship_to_phone' => $validated['ship_to_phone'] ?? null,
    ]);

    // Gather submitted item IDs
    $submittedItemIds = collect($validated['items'])
        ->pluck('id')
        ->filter()
        ->map(fn($id) => (int)$id)
        ->toArray();

    // Remove deleted items
    DB::table('fgp_order_items')
        ->where('fgp_order_id', $order->id)
        ->whereNotIn('id', $submittedItemIds)
        ->delete();

    // Update or create items
    foreach ($validated['items'] as $item) {
        if (!empty($item['id'])) {
            // Update existing
            DB::table('fgp_order_items')
                ->where('id', $item['id'])
                ->update([
                    'fgp_item_id' => $item['fgp_item_id'],
                    'quantity' => $item['unit_number'],
                    'unit_price' => $item['unit_cost'],
                    'price' => $item['unit_cost'] * $item['unit_number'],
                    'updated_at' => now(),
                ]);
        } else {
            // Add new
            DB::table('fgp_order_items')->insert([
                'fgp_order_id' => $order->id,
                'fgp_item_id' => $item['fgp_item_id'],
                'quantity' => $item['unit_number'],
                'unit_price' => $item['unit_cost'],
                'price' => $item['unit_cost'] * $item['unit_number'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return redirect()
    ->route('franchise.orders', ['franchise' => $franchiseId])
    ->with('success', 'Order #' . $order->getOrderNum() . ' updated successfully!');
}

public function createPackingList($orderId)
{
    // Fetch the order and its shipments
    $order = FgpOrder::findOrFail($orderId);
    $shipments = UpsShipment::where('fgp_ordersID', $orderId)->get();

    // Decode box_contents for each shipment
    foreach ($shipments as $shipment) {
        $shipment->box_contents = is_string($shipment->box_contents)
            ? json_decode($shipment->box_contents, true)
            : $shipment->box_contents;
    }

    return view('corporate_admin.view_orders.packinglist', compact('order', 'shipments'));
}


    public function orderposps($franchise)
    {

        $franchiseId = intval($franchise);

        if (request()->ajax()) {
            $currentMonth = strval(Carbon::now()->format('n'));
            $franchiseId = session('franchise_id');


            $pops = FgpItem::where('franchise_id', $franchiseId)
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

        $franchiseId = $franchise;

        return view('corporate_admin.orderpops.index', compact('totalPops', 'franchiseId'));
    }


    public function confirmPage(){
        $items = session('ordered_items', []);

        if (empty($items)) {
            return redirect()->route('franchise.orderpops.index')->withErrors('No items selected.');
        }

        $requiredCharges = AdditionalCharge::where('charge_optional', 'required')->where('status', 1)->get();
        $optionalCharges = AdditionalCharge::where('charge_optional', 'optional')->where('status', 1)->get();
        $users = Franchise::get();
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


    public function store(Request $request, $franchiseId)
    {
     
        // If corporate_admin selected a franchise from dropdown, use that instead of route parameter
        if ($request->filled('franchise_id') && Auth::check() && Auth::user()->role == 'corporate_admin') {
            $franchiseId = intval($request->input('franchise_id'));
        } else {
            $franchiseId = intval($franchiseId);
        }
        // dd( $franchiseId);
        
        // dd($franchiseId,$request->all());
        $minCases = 12; // Can be made configurable via settings
        $factorCase = 3; // Can be made configurable via settings
        
        // Validate request
        $validated = $request->validate([
            'franchise_id' => Auth::check() && Auth::user()->role == 'corporate_admin' ? 'required|exists:franchises,id' : 'nullable|exists:franchises,id', // Require for corporate_admin
            'items' => 'required|array',
            'items.*.fgp_item_id' => 'required|exists:fgp_items,id',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.unit_number' => 'required|integer|min:1',
            // Shipping information validation
            'ship_to_name' => 'nullable|string|max:255',
            'ship_to_address1' => 'nullable|string|max:255',
            'ship_to_address2' => 'nullable|string|max:255',
            'ship_to_city' => 'nullable|string|max:255',
            'ship_to_state' => 'nullable|string|max:255',
            'ship_to_zip' => 'nullable|string|max:20',
            'ship_to_phone' => 'nullable|string|max:50',
        ]);

        // Calculate total case quantity
        $totalCaseQty = collect($validated['items'])->sum('unit_number');

        // Check if the total order quantity meets the minimum required cases
        // if ($totalCaseQty < $minCases) {
        //     return redirect()->back()->withErrors(['Order must have at least ' . $minCases . ' cases.']);
        // }

        // Check if the total order quantity is a valid multiple of the factor case
        // if ($totalCaseQty % $factorCase !== 0) {
        //     return redirect()->back()->withErrors(['Order quantity must be a multiple of ' . $factorCase . '.']);
        // }

        // Calculate total amount
        $totalAmount = collect($validated['items'])->sum(function ($item) {
            return $item['unit_cost'] * $item['unit_number'];
        });

        $order = FgpOrder::create([
            'franchise_id' => $franchiseId,
            'order_num' => 'FGP-' . time() . '-' . rand(1000, 9999),
            'amount' => $totalAmount,
            // 'date_transaction' => now(),
            'ship_to_name' => $validated['ship_to_name'] ?? null,
            'ship_to_address1' => $validated['ship_to_address1'] ?? null,
            'ship_to_address2' => $validated['ship_to_address2'] ?? null,
            'ship_to_city' => $validated['ship_to_city'] ?? null,
            'ship_to_state' => $validated['ship_to_state'] ?? null,
            'ship_to_zip' => $validated['ship_to_zip'] ?? null,
            'ship_to_phone' => $validated['ship_to_phone'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            DB::table('fgp_order_items')->insert([
                'fgp_order_id' => $order->id,
                'fgp_item_id' => $item['fgp_item_id'],
                'unit_price' => $item['unit_cost'],
                'quantity' => $item['unit_number'],
                'price' => $item['unit_cost'] * $item['unit_number'],
               // 'unit_number' => $item['unit_number'], // Keep for compatibility
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('franchise.orders', ['franchise' => $franchiseId])
            ->with('success', 'Order #' . $order->order_num . ' created successfully!');
    }

    /**
     * Update order status - specifically handles order cancellation via soft delete
     */
    public function updateStatus(Request $request, $franchiseId)
    {
        try {
            $orderId = $request->input('order_id');
            $status = $request->input('status');

            $order = FgpOrder::findOrFail($orderId);

            if ($status === 'cancelled') {
                // Perform soft delete
                $order->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Order #' . $order->getOrderNum() . ' has been cancelled successfully.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid status provided.'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling order: ' . $e->getMessage()
            ], 500);
        }
    }
}
