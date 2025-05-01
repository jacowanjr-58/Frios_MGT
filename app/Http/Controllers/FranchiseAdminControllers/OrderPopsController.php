<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use Carbon\Carbon;
use App\Models\FpgItem;
use App\Models\FpgOrder;
use App\Models\FpgCategory;
use Illuminate\Http\Request;
use App\Models\AdditionalCharge;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FgpOrder;
use DB;

class OrderPopsController extends Controller
{
    public function index()
    {
        $currentMonth = strval(Carbon::now()->format('n')); // Get current month as single-digit (1-12)

        // Fetch only items that are orderable, in stock, and available in the current month
        $pops = FpgItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0) // Ensure the item is in stock
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

        // Count total available flavor pops
        $totalPops = $pops->count();

        return view('franchise_admin.orderpops.index', compact('pops', 'totalPops'));
    }

    public function create()
    {
        $currentMonth = strval(Carbon::now()->format('n'));

        // Fetch only orderable, in-stock, and currently available items
        $pops = FpgItem::where('orderable', 1)
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

        return view('franchise_admin.orderpops.create', compact('categorizedItems'));
    }

    // public function confirmOrder(Request $request)
    // {
    //     \Log::info('Received Order Data:', ['ordered_items' => $request->input('ordered_items')]);

    //     // Decode the JSON input
    //     $items = json_decode($request->input('ordered_items'), true) ?: [];

    //     if (empty($items)) {
    //         \Log::warning('No items received in order confirmation.');
    //     }

    //     // Fetch additional charges from the database
    //     $requiredCharges = AdditionalCharge::where('charge_optional', 'required')->get();
    //     $optionalCharges = AdditionalCharge::where('charge_optional', 'optional')->get();

    //     return view('franchise.orderpops.confirm', compact('items', 'requiredCharges', 'optionalCharges'));
    // }

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

        return response()->json(['redirect' => route('franchise.orderpops.confirm.page')]);
    } catch (\Exception $e) {
        \Log::error('Error in confirmOrder method: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
    }
}

public function showConfirmPage()
{
    $items = session('ordered_items', []);

    if (empty($items)) {
        return redirect()->route('franchise.orderpops.index')->withErrors('No items selected.');
    }

    $requiredCharges = AdditionalCharge::where('charge_optional', 'required')->where('status', 1)->get();
    $optionalCharges = AdditionalCharge::where('charge_optional', 'optional')->where('status', 1)->get();

    return view('franchise_admin.orderpops.confirm', compact('items', 'requiredCharges', 'optionalCharges'));
}


public function store(Request $request)
{
    $minCases = 12; // Can be made configurable via settings
    $factorCase = 3; // Can be made configurable via settings

    // Validate request
    $validated = $request->validate([

        'items' => 'required|array',
        'items.*.fgp_item_id' => 'required|exists:fpg_items,fgp_item_id',
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

    $orders = FpgOrder::create([
        'user_ID' => Auth::id(),
        'date_transaction' => now(),
        'status' => 'Pending',
    ]);

    foreach ($validated['items'] as $item) {
        DB::table('fgp_order_details')->insert([
            'fpg_order_id' => $orders->id,
            'fgp_item_id' => $item['fgp_item_id'],
            'unit_cost' => $item['unit_cost'],
            'unit_number' => $item['unit_number'],
            'date_transaction' => now(),
            'ACH_data' => null,
        ]);
    }

    return redirect()->route('franchise.orderpops.index')->with('success', 'Order placed successfully!');
}


public function viewOrders()
{
    // $orders = FpgOrder::where('user_ID', Auth::id())
    //     ->select(
    //         'user_ID',
    //         'date_transaction',
    //         \DB::raw('SUM(unit_number) as total_quantity'),
    //         \DB::raw('SUM(unit_number * unit_cost) as total_amount'),
    //         'status'
    //     )
    //     ->groupBy('date_transaction', 'user_ID', 'status')
    //     ->orderBy('date_transaction', 'desc')
    //     ->with('user')
    //     ->get()
    //     ->map(function ($order) {
    //         $order->date_transaction = Carbon::parse($order->date_transaction);
    //         return $order;
    //     });

    $orders = FpgOrder::where('user_ID' , Auth::id())->get();

    $totalOrders = $orders->count();

    return view('franchise_admin.orderpops.vieworders', compact('orders', 'totalOrders'));
}


}

