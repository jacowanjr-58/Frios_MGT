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
     */
    public function create()
    {
        $franchiseId = Auth::user()->franchisee_id;
        $fgpItems = FgpItem::orderBy('name')->get();

        return view('franchise_admin.inventory.create', compact('fgpItems'));
    }

    /**
     * Store a newly created inventory master record in storage.
     * This is whre we could add something from fgp_item (that doesn't come through order process, like onboarding)
     * or could use to add custom_items (not fully schemed in db)
     */
    public function store(Request $request)
    {
        $franchiseId = Auth::user()->franchisee_id;

        $request->validate([
            'fgp_item_id'       => ['nullable', 'exists:fgp_items,fgp_item_id'],
            'custom_item_name'  => ['nullable', 'string', 'max:255'],
            'total_quantity'    => ['required', 'integer', 'min:0'],
        ]);

        $fgpItemId      = $request->input('fgp_item_id');
        $customItemName = trim($request->input('custom_item_name') ?? '');

        // Ensure exactly one of fgp_item_id or custom_item_name is provided
        if (!$fgpItemId && $customItemName === '') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['Either select a Pop flavor or enter a custom item name.']);
        }
        if ($fgpItemId && $customItemName !== '') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['Please either select a Pop flavor or enter a custom item name, not both.']);
        }

        // Prevent duplicate master lines for same franchisee + fgp_item_id or custom_item_name
        if ($fgpItemId) {
            $exists = InventoryMaster::where('franchisee_id', $franchiseId)
                ->where('fgp_item_id', $fgpItemId)
                ->first();
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['An inventory line for that Pop flavor already exists.']);
            }
        } else {
            $exists = InventoryMaster::where('franchisee_id', $franchiseId)
                ->whereNull('fgp_item_id')
                ->where('custom_item_name', $customItemName)
                ->first();
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['A custom inventory line with that name already exists.']);
            }
        }

        InventoryMaster::create([
            'franchisee_id'    => $franchiseId,
            'fgp_item_id'      => $fgpItemId,
            'custom_item_name' => $fgpItemId ? null : $customItemName,
            'total_quantity'   => $request->input('total_quantity'),
        ]);

        return redirect()
            ->route('franchise.inventory.index')
            ->with('success', 'Inventory line created successfully.');
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
        'pops_on_hand'          => 'nullable|integer|min:0',
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
            'pops_on_hand'          => $data['pops_on_hand'] ?? 0,
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
