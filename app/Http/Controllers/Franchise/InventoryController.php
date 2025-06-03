<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\InventoryMaster;
use App\Models\FgpItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /**
     * Display a listing of the franchisee's inventory master records.
     */
    public function index()
    {
        $franchiseId = Auth::user()->franchisee_id;

        $inventoryMasters = InventoryMaster::with('flavor')
            ->where('franchisee_id', $franchiseId)
            ->orderBy('custom_item_name')
            ->orderBy('fgp_item_id')
            ->paginate(20);

        return view('franchise_admin.inventory.index', compact('inventoryMasters'));
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
     */
    public function edit(InventoryMaster $inventoryMaster)
    {
        $this->authorize('update', $inventoryMaster);

        $franchiseId = Auth::user()->franchisee_id;
        if ($inventoryMaster->franchisee_id !== $franchiseId) {
            abort(403);
        }

        $fgpItems = FgpItem::orderBy('name')->get();
        return view('franchise_admin.inventory.edit', compact('inventoryMaster', 'fgpItems'));
    }

    /**
     * Update the specified inventory master record in storage.
     */
    public function update(Request $request, InventoryMaster $inventoryMaster)
    {
        $this->authorize('update', $inventoryMaster);

        $franchiseId = Auth::user()->franchisee_id;
        if ($inventoryMaster->franchisee_id !== $franchiseId) {
            abort(403);
        }

        $request->validate([
            'fgp_item_id'       => ['nullable', 'exists:fgp_items,fgp_item_id'],
            'custom_item_name'  => ['nullable', 'string', 'max:255'],
            'total_quantity'    => ['required', 'integer', 'min:0'],
        ]);

        $fgpItemId      = $request->input('fgp_item_id');
        $customItemName = trim($request->input('custom_item_name') ?? '');

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

        // Prevent duplicates except for this record
        if ($fgpItemId) {
            $exists = InventoryMaster::where('franchisee_id', $franchiseId)
                ->where('fgp_item_id', $fgpItemId)
                ->where('inventory_id', '!=', $inventoryMaster->inventory_id)
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
                ->where('inventory_id', '!=', $inventoryMaster->inventory_id)
                ->first();
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['A custom inventory line with that name already exists.']);
            }
        }

        $inventoryMaster->update([
            'fgp_item_id'      => $fgpItemId,
            'custom_item_name' => $fgpItemId ? null : $customItemName,
            'total_quantity'   => $request->input('total_quantity'),
        ]);

        return redirect()
            ->route('franchise.inventory.index')
            ->with('success', 'Inventory line updated successfully.');
    }

    /**
     * Remove the specified inventory master record from storage.
     */
    public function destroy(InventoryMaster $inventoryMaster)
    {
        $this->authorize('delete', $inventoryMaster);

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
