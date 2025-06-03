<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\InventoryMaster;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryAdjustmentController extends Controller
{
    public function adjustForm()
    {
        $franchiseId = Auth::user()->franchisee_id;
        $inventoryMasters = InventoryMaster::where('franchisee_id', $franchiseId)->get();
        return view('franchise_admin.inventory.adjust', [
            'inventoryMasters' => $inventoryMasters
        ]);
    }

    public function adjustUpdate(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventory_master,inventory_id',
            'type'         => 'required|in:add,remove',
            'quantity'     => 'required|integer|min:1',
            'notes'        => 'nullable|string'
        ]);

        $inventoryId = $request->input('inventory_id');
        $type        = $request->input('type');
        $quantity    = $request->input('quantity');
        $notes       = $request->input('notes', null);

        $inventory = InventoryMaster::findOrFail($inventoryId);

        if ($type === 'add') {
            $inventory->increment('total_quantity', $quantity);
            InventoryTransaction::create([
                'inventory_id' => $inventoryId,
                'type'         => 'add',
                'quantity'     => $quantity,
                'reference'    => 'Manual Adjust +',
                'notes'        => $notes,
                'created_by'   => Auth::id()
            ]);
        } else {
            if ($inventory->total_quantity < $quantity) {
                return redirect()->back()->with('error', 'Not enough stock to remove.');
            }
            $inventory->decrement('total_quantity', $quantity);
            InventoryTransaction::create([
                'inventory_id' => $inventoryId,
                'type'         => 'remove',
                'quantity'     => $quantity,
                'reference'    => 'Manual Adjust -',
                'notes'        => $notes,
                'created_by'   => Auth::id()
            ]);
        }

        return redirect()->back()->with('success', 'Inventory adjusted successfully.');
    }
}
