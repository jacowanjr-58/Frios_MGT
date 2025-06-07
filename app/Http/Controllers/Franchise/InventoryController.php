<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\InventoryMaster;
use App\Models\FgpItem;
use App\Models\InventoryLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the franchisee's inventory master records.
     */
    public function index(Request $request)
    {
        $franchiseId = Auth::user()->franchisee_id;

        $inventories = InventoryMaster::with('flavor')        // eager-load the FgpItem
            ->where('franchisee_id', $franchiseId)             // only this franchisee
            ->where('total_quantity', '>', 0)                  // only “active” (qty > 0)
            ->orderBy('fgp_item_id')
            ->paginate(20);

        return view('franchise_admin.inventory.index', compact('inventories'));
    }


    /**
     * Show the form for creating a new inventory master record.
     * Create either an item from Corporate FGP items or a custom item.
     */
   public function create()
{
    $franchiseId = Auth::user()->franchisee_id;
    $fgpItems    = FgpItem::orderBy('name')->get();
    $locations   = InventoryLocation::where('franchisee_id', $franchiseId)
                     ->orderBy('name')
                     ->get();
    $today       = now()->format('Y-m-d');

    return view('franchise_admin.inventory.create', compact(
        'fgpItems',
        'locations',
        'today'
    ));
}

    /**
     * Store a newly created inventory master record in storage.
     *
     * This method handles both corporate items (by fpg_items_id) and custom items (by name).
     */
    public function store(Request $request)
{
    $franchiseId = Auth::user()->franchisee_id;
    $data = $request->validate([
        'fgp_item_id'       => ['nullable','exists:fgp_items,fgp_item_id'],
        'custom_item_name'  => ['nullable','string','max:255'],
        'stock_count_date'  => ['required','date'],
        'total_quantity'    => ['required','integer','min:0'],
        'allocations'       => ['required','array'],
        'allocations.*'     => ['required','integer','min:0'],
    ]);

    // require exactly one of corporate or custom
    if (!($data['fgp_item_id'] xor $data['custom_item_name'])) {
        return back()
            ->withInput()
            ->withErrors(['fgp_item_id' => 'Select a corporate item or enter a custom name, not both.']);
    }

    // allocation sum check
    if (array_sum($data['allocations']) !== (int)$data['total_quantity']) {
        return back()
            ->withInput()
            ->withErrors(['allocations' => 'Sum of allocations must equal total quantity.']);
    }

    DB::transaction(function() use ($data, $franchiseId) {
        $master = InventoryMaster::create([
            'franchisee_id'    => $franchiseId,
            'fgp_item_id'      => $data['fgp_item_id'],
            'custom_item_name' => $data['custom_item_name'],
            'stock_count_date' => $data['stock_count_date'],
            'total_quantity'   => $data['total_quantity'],
            'split_total_quantity' => $data['split_total_quantity'], // assuming split is same as total for now
        ]);

        foreach ($data['allocations'] as $locId => $qty) {
            if ($qty > 0) {
                $master->allocations()->create([
                    'location_id'        => $locId,
                    'allocated_quantity' => $qty,
                ]);
            }
        }
    });

    return redirect()
        ->route('franchise.inventory.index')
        ->with('success', 'Inventory created successfully.');
}
    /**
     * Show the form for editing the specified inventory master record.
     * Currently have the item name as lable because only have fgp_Items right now
     */
    public function edit(InventoryMaster $inventoryMaster)
    {
         $franchiseId = Auth::user()->franchisee_id;
    //dd($inventoryMaster,$franchiseId );

      //  $this->authorize('update', $inventoryMaster); //Could use a policy


        // Load the related FgpItem (for item_name) and allocations (for the grid)
    $inventoryMaster->load('flavor', 'allocations');

    $franchiseId = Auth::user()->franchisee_id;
    if ($inventoryMaster->franchisee_id !== $franchiseId) {
        abort(403);
    }

    // Pull your list of possible locations
    $locations = InventoryLocation::where('franchisee_id', $franchiseId)
                         ->orderBy('name')
                         ->get();

    // Build a simple [location_ID => quantity] array
    $existingAllocations = $inventoryMaster->allocations
        ->pluck('allocated_quantity', 'location_id')
        ->toArray();

    return view('franchise_admin.inventory.edit', compact(
        'inventoryMaster',
        'locations',
        'existingAllocations'
    ));
    }

    /**
     * Update the specified inventory master record in storage.
     */
    public function update(Request $request, InventoryMaster $inventoryMaster)
{
    $franchiseId = Auth::user()->franchisee_id;
    if ($inventoryMaster->franchisee_id !== $franchiseId) {
        abort(403);
    }

    $data = $request->validate([
        'stock_count_date'      => 'required|date',
        'split_total_quantity'  => 'nullable|integer|min:0',
        'whole_sale_price_case' => 'nullable|numeric|min:0',
        'retail_price_pop'      => 'nullable|numeric|min:0',
        'total_quantity'        => 'required|integer|min:0',
        'allocations'           => 'required|array',
        'allocations.*'         => 'required|integer|min:0',
    ]);

    // Ensure allocations sum to the master total
    if (array_sum($data['allocations']) != $data['total_quantity']) {
        return back()
            ->withInput()
            ->withErrors(['allocations' =>
                'Sum of allocations must equal total quantity.']);
    }

    DB::transaction(function() use ($inventoryMaster, $data) {
        // 1) Update the master row
        $inventoryMaster->update([
            'stock_count_date'      => $data['stock_count_date'],
            'split_total_quantity'  => $data['split_total_quantity'] ?? 0,
            'whole_sale_price_case' => $data['whole_sale_price_case'] ?? 0,
            'retail_price_pop'      => $data['retail_price_pop'] ?? 0,
            'total_quantity'        => $data['total_quantity'],
        ]);

        // 2) Clear and re-sync allocations via the relation
        $inventoryMaster->allocations()->delete();

        foreach ($data['allocations'] as $locId => $qty) {

                $inventoryMaster->allocations()->create([
                    'location_id' => $locId,
                    'allocated_quantity'     => $qty,
                ]);

        }
    });

    return redirect()
        ->route('franchise.inventory.index')
        ->with('success', 'Inventory updated successfully.');
}

    /**
     * Remove the specified inventory master record from storage.
     */
    public function destroy(InventoryMaster $inventoryMaster)
    {
      //  $this->authorize('delete', $inventoryMaster);

        $franchiseId = Auth::user()->franchisee_id;
        if ($inventoryMaster->franchisee_id !== $franchiseId) {
            abort(403);
        }

        // Deleting the master row will cascade to allocations & transactions via FKs
        $inventoryMaster->delete();

        return redirect()
            ->route('franchise.inventory.index')
            ->with('success', 'Inventory line deleted.');
    }
}
