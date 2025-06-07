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

    // 1️⃣ Validation
    $data = $request->validate([
        'fgp_item_id'        => ['nullable','exists:fgp_items,fgp_item_id'],
        'custom_item_name'   => ['nullable','string','max:255'],
        'stock_count_date'   => ['required','date'],
        'total_quantity'     => ['required','integer','min:0'],
        'split_factor'       => ['required','integer','min:1'],        // units per case
        'cogs_case'          => ['nullable','numeric','min:0'],        // cost of one case
        'cogs_unit'          => ['nullable','numeric','min:0'],        // cost of one unit
        'wholesale_case'     => ['nullable','numeric','min:0'],
        'wholesale_unit'     => ['nullable','numeric','min:0'],
        'retail_case'        => ['nullable','numeric','min:0'],
        'retail_unit'        => ['nullable','numeric','min:0'],

        // now nested allocations: each location has cases + units
        'allocations'            => ['required','array'],
        'allocations.*.cases'    => ['required','integer','min:0'],
        'allocations.*.units'    => ['required','integer','min:0'],
    ]);

    // 2️⃣ Exactly one of corporate vs custom
    if (! ($data['fgp_item_id'] xor $data['custom_item_name'])) {
        return back()
            ->withInput()
            ->withErrors(['fgp_item_id' => 'Either select a corporate item or enter a custom name, not both.']);
    }

    // 3️⃣ Sum up allocations in base units and compare to total_quantity
    $sum = 0;
    foreach ($data['allocations'] as $locId => $alloc) {
        $sum += $alloc['cases'] * $data['split_factor']
              + $alloc['units'];
    }
    if ($sum !== (int)$data['total_quantity']) {
        return back()
            ->withInput()
            ->withErrors(['allocations' =>
                "Sum of allocations ({$sum}) must equal total quantity ({$data['total_quantity']})."
            ]);
    }

    // 4️⃣ Persist master + allocations in a transaction
    DB::transaction(function() use ($data, $franchiseId) {
        $master = InventoryMaster::create([
            'franchisee_id'     => $franchiseId,
            'fgp_item_id'       => $data['fgp_item_id'],
            'custom_item_name'  => $data['custom_item_name'],
            'stock_count_date'  => $data['stock_count_date'],
            'total_quantity'    => $data['total_quantity'],
            'split_factor'      => $data['split_factor'],
            'cogs_case'         => $data['cogs_case'],
            'cogs_unit'         => $data['cogs_unit'],
            'wholesale_case'    => $data['wholesale_case'],
            'wholesale_unit'    => $data['wholesale_unit'],
            'retail_case'       => $data['retail_case'],
            'retail_unit'       => $data['retail_unit'],
        ]);

        foreach ($data['allocations'] as $locId => $alloc) {
            $cases   = $alloc['cases'];
            $units   = $alloc['units'];
            $quantity = $cases * $master->split_factor + $units;

            // only save non-zero allocations
            if ($quantity > 0) {
                $master->allocations()->create([
                    'location_id'        => $locId,
                    'allocated_cases'    => $cases,
                    'allocated_units'    => $units,
                    'allocated_quantity' => $quantity,
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

    
      // 3️⃣ Build [ location_id => ['cases'=>…, 'units'=>…] ]:
    $existingAllocations = $inventoryMaster->allocations
        ->mapWithKeys(function($alloc) use ($inventoryMaster) {
            $split  = $inventoryMaster->split_factor;
            $qty    = $alloc->allocated_quantity;
            return [
                $alloc->location_id => [
                    'cases' => intdiv($qty, $split),
                    'units' => $qty % $split,
                ]
            ];
        })
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

    // 1️⃣ Validate, including split_factor, costs, and nested allocations
    $data = $request->validate([
        'stock_count_date'      => 'required|date',
        'split_factor'          => 'required|integer|min:1',
        'cogs_case'             => 'nullable|numeric|min:0',
        'cogs_unit'             => 'nullable|numeric|min:0',
        'wholesale_case'        => 'nullable|numeric|min:0',
        'wholesale_unit'        => 'nullable|numeric|min:0',
        'retail_case'           => 'nullable|numeric|min:0',
        'retail_unit'           => 'nullable|numeric|min:0',
        'total_quantity'        => 'required|integer|min:0',
        'allocations'           => 'required|array',
        'allocations.*.cases'   => 'required|integer|min:0',
        'allocations.*.units'   => 'required|integer|min:0',
    ]);

    // 2️⃣ Compute and verify sums
    $computed = 0;
    foreach ($data['allocations'] as $locId => $alloc) {
        $computed += $alloc['cases'] * $data['split_factor']
                   + $alloc['units'];
    }
    if ($computed !== (int)$data['total_quantity']) {
        return back()
            ->withInput()
            ->withErrors(['allocations' =>
                "Sum of allocations ({$computed}) must equal total quantity ({$data['total_quantity']})."
            ]);
    }

    // 3️⃣ Persist master + allocations atomically
    DB::transaction(function() use ($inventoryMaster, $data) {
        // Update the master record
        $inventoryMaster->update([
            'stock_count_date'      => $data['stock_count_date'],
            'split_factor'          => $data['split_factor'],
            'cogs_case'             => $data['cogs_case'] ?? 0,
            'cogs_unit'             => $data['cogs_unit'] ?? 0,
            'wholesale_case'        => $data['wholesale_case'] ?? 0,
            'wholesale_unit'        => $data['wholesale_unit'] ?? 0,
            'retail_case'           => $data['retail_case'] ?? 0,
            'retail_unit'           => $data['retail_unit'] ?? 0,
            'total_quantity'        => $data['total_quantity'],
        ]);

        // Clear out old allocations
        $inventoryMaster->allocations()->delete();

        // Re‐create only non‐zero allocations
        foreach ($data['allocations'] as $locId => $alloc) {
            $cases    = $alloc['cases'];
            $units    = $alloc['units'];
            $quantity = $cases * $inventoryMaster->split_factor + $units;

            if ($quantity > 0) {
                $inventoryMaster->allocations()->create([
                    'location_id'        => $locId,
                    'allocated_cases'    => $cases,
                    'allocated_units'    => $units,
                    'allocated_quantity' => $quantity,
                ]);
            }
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
