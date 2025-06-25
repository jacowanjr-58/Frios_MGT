<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\InventoryMaster;
use App\Models\InventoryTransaction;
use App\Models\FgpItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryReceiveController extends Controller
{
    public function receiveForm()
    {
        $franchiseId = Auth::user()->franchise_id;
        $deliveredPopFlavors = FgpItem::whereHas('deliveries', function ($q) use ($franchiseId) {
            $q->where('franchise_id', $franchiseId);
        })->select('fgp_item_id', 'name')->get();

        return view('franchise_admin.inventory.receive', [
            'deliveredPopFlavors' => $deliveredPopFlavors
        ]);
    }

    public function receiveStore(Request $request)
    {
        $request->validate([
            'quantity'         => 'required|integer|min:1',
            'reference'        => 'nullable|string|max:255',
            'is_custom'        => 'sometimes|boolean',
            'fgp_item_id'      => 'nullable|exists:fgp_items,fgp_item_id',
            'custom_item_name' => 'nullable|string|max:255',
        ]);

        $franchiseId = Auth::user()->franchise_id;
        $quantity = $request->input('quantity');
        $reference = $request->input('reference', null);

        if ($request->filled('is_custom')) {
            $customName = $request->input('custom_item_name');
            $inventory = InventoryMaster::firstOrCreate(
                ['franchise_id' => $franchiseId, 'fgp_item_id' => null, 'custom_item_name' => $customName],
                ['total_quantity' => 0]
            );
        } else {
            $fgpItemId = $request->input('fgp_item_id');
            $inventory = InventoryMaster::firstOrCreate(
                ['franchise_id' => $franchiseId, 'fgp_item_id' => $fgpItemId],
                ['custom_item_name' => null, 'total_quantity' => 0]
            );
        }

        $inventory->increment('total_quantity', $quantity);

        InventoryTransaction::create([
            'inventory_id'  => $inventory->inventory_id,
            'type'          => 'add',
            'quantity'      => $quantity,
            'reference'     => $reference,
            'notes'         => 'Received stock',
            'created_by'    => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Stock received successfully.');
    }
}
