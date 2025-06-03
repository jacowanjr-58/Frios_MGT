<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\FgpItem;
use App\Models\FgpOrder;
use App\Models\InventoryAllocation;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class InventoryController extends Controller
{

     /**
     * Display a listing of inventory records.
     */
     public function index()
    {
        // Only show inventory for the current franchisee
        $franchiseeId = Auth::user()->franchisee_id;

        $inventories = Inventory::with(['item', 'location'])
            ->where('franchisee_id', $franchiseeId)
            ->orderBy('stock_count_date', 'desc')
            ->paginate(25);

        return view('franchise_admin.inventory.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new inventory record.
     */
    public function create()
    {
        $franchiseeId = Auth::user()->franchisee_id;

        // All items for the dropdown
        $items = FgpItem::all();
        // All locations for this franchisee
        $locations = Location::where('franchisee_id', $franchiseeId)->get();

        return view('franchise_admin.inventory.create', compact('items', 'locations'));
    }

    /**
     * Store a newly created inventory record in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fgp_item_id'           => 'required|exists:fgp_items,fgp_item_id',
            'stock_on_hand'         => 'required|integer|min:0',
            'stock_count_date'      => 'required|date',
            'locations_ID'          => 'nullable|exists:locations,locations_ID',
            'pops_on_hand'          => 'nullable|integer|min:0',
            'whole_sale_price_case' => 'nullable|numeric|min:0',
            'retail_price_pop'      => 'nullable|numeric|min:0',
        ]);

        // Inject franchisee_id automatically
        $data['franchisee_id'] = Auth::user()->franchisee_id;

        Inventory::create($data);

        return redirect()
            ->route('franchise.inventory.index')
            ->with('success', 'Inventory record created successfully.');
    }

    /**
     * Show the form for editing the specified inventory record.
     */
    public function edit(Inventory $inventory)
    {
        // Only allow editing if it belongs to this franchisee
        $this->authorize('update', $inventory);

        $franchiseeId = Auth::user()->franchisee_id;
        $items = FgpItem::all();
        $locations = Location::where('franchisee_id', $franchiseeId)->get();

        return view('franchise_admin.inventory.edit', compact('inventory', 'items', 'locations'));
    }

    /**
     * Update the specified inventory record in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);

        $data = $request->validate([
            'fgp_item_id'           => 'required|exists:fgp_items,fgp_item_id',
            'stock_on_hand'         => 'required|integer|min:0',
            'stock_count_date'      => 'required|date',
            'locations_ID'          => 'nullable|exists:locations,locations_ID',
            'pops_on_hand'          => 'nullable|integer|min:0',
            'whole_sale_price_case' => 'nullable|numeric|min:0',
            'retail_price_pop'      => 'nullable|numeric|min:0',
        ]);

        $inventory->update($data);

        return redirect()
            ->route('franchise.inventory.index')
            ->with('success', 'Inventory record updated successfully.');
    }

    /**
     * Remove the specified inventory record from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $this->authorize('delete', $inventory);
        $inventory->delete();

        return redirect()
            ->route('franchise.inventory.index')
            ->with('success', 'Inventory record deleted.');
    }


    public function inventoryDetail(Request $request)
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



    public function inventoryLocations()
    {
        try {
            $flavors = FgpItem::all();
            $locations = Location::where('franchisee_id', Auth::user()->franchisee_id)->get();
            $initialPopFlavors = [];
            foreach ($flavors as $flavor) {
                $initialPopFlavors[] = [
                    'name' => $flavor->name,
                    'image1' => $flavor->image1,
                    'available' => $flavor->availableQuantity(),
                ];
            }
            // dd($initialPopFlavors);

            $allocatedInventory = InventoryAllocation::join('fgp_items', 'fgp_items.fgp_item_id', '=', 'inventory_allocations.fgp_item_id')
                ->select('fgp_items.name as flavor', 'inventory_allocations.location', 'inventory_allocations.quantity as cases')
                ->where('franchise_id', Auth::user()->franchisee_id)
                ->get();

            return view('franchise_admin.inventory.locations', compact(
                'flavors',
                'initialPopFlavors',
                'allocatedInventory',
                'locations'
            ));
        } catch (\Exception $e) {
            // Log error or dd for debug
            dd('Error: ' . $e->getMessage());
        }
    }


    public function allocateInventory(Request $request)
{
    try {
        $franchiseId = Auth::user()->franchisee_id;

        foreach ($request->allocatedInventory as $item) {
            // 1) Try to find a matching fgp_item_id by name
            $flavorName = $item['flavor'];
            $fgpItem = FgpItem::where('name', $flavorName)->first();

            if ($fgpItem) {
                // Real Pop from fgp_items
                $fgp_item_id      = $fgpItem->fgp_item_id;
                $custom_item_name = null;
            } else {
                // No match in fgp_items → treat as a custom item
                $fgp_item_id      = null;
                $custom_item_name = $flavorName;
            }

            $location = $item['location'];
            $cases    = $item['cases'];

            // 2) See if an allocation already exists for this franchise/location + (either fgp_item_id or custom_item_name)
            if ($fgp_item_id) {
                // Match on fgp_item_id + location
                $exists = InventoryAllocation::where('franchise_id', $franchiseId)
                    ->where('fgp_item_id', $fgp_item_id)
                    ->where('location', $location)
                    ->first();
            } else {
                // Match on custom_item_name + location
                $exists = InventoryAllocation::where('franchise_id', $franchiseId)
                    ->whereNull('fgp_item_id')
                    ->where('custom_item_name', $custom_item_name)
                    ->where('location', $location)
                    ->first();
            }

            if ($exists) {
                // 3a) Update quantity if it already exists
                $exists->update([
                    'quantity'         => $cases,
                    // We leave fgp_item_id or custom_item_name as-is
                ]);
            } else {
                // 3b) Create a new allocation row
                InventoryAllocation::create([
                    'franchise_id'     => $franchiseId,
                    'fgp_item_id'      => $fgp_item_id,       // either int or null
                    'custom_item_name' => $custom_item_name,  // either string or null
                    'location'         => $location,
                    'quantity'         => $cases,
                ]);
            }
        }

        return response()->json([
            'error'   => false,
            'message' => 'Inventory allocated successfully',
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'error'   => true,
            'message' => $th->getMessage(),
        ], 500);
    }
}


    public function updateQuantity(Request $request)
    {
        try {
            $fgp_item_id = FgpItem::where('name', $request->flavor)->first()->fgp_item_id ?? null;

            if (!$fgp_item_id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid flavor'
                ]);
            }

            $allocation = InventoryAllocation::where('franchise_id', Auth::user()->franchisee_id)
                ->where('fgp_item_id', $fgp_item_id)
                ->where('location', $request->location)
                ->first();

            if ($allocation) {
                if ($allocation->quantity <= 1) {
                    $allocation->delete();
                    return response()->json([
                        'error' => false,
                        'message' => 'Item deleted because quantity reached zero'
                    ]);
                } else {
                    $allocation->decrement('quantity');
                    return response()->json([
                        'error' => false,
                        'message' => 'Quantity decreased by 1'
                    ]);
                }
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Allocation not found'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }


    public function removeItem(Request $request)
    {
        try {
            $fgp_item_id = FgpItem::where('name', $request->flavor)->first()->fgp_item_id ?? null;

            if (!$fgp_item_id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid flavor'
                ]);
            }

            InventoryAllocation::where('franchise_id', Auth::user()->franchisee_id)
                ->where('fgp_item_id', $fgp_item_id)
                ->where('location', $request->location)
                ->delete();

            return response()->json([
                'error' => false,
                'message' => 'Item removed successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }
}
