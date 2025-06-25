<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\InventoryRemovalQueue;
use App\Models\InventoryAllocation;
use App\Models\InventoryMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryRemovalController extends Controller
{
    public function showRemovalQueue()
    {
        $franchiseId = Auth::user()->franchise_id;

        $removals = InventoryRemovalQueue::with(['inventoryMaster.flavor', 'location', 'requestedBy'])
            ->whereHas('inventoryMaster', function ($q) use ($franchiseId) {
                $q->where('franchise_id', $franchiseId);
            })
            ->where('status', 'pending')
            ->get();

        return view('franchise_admin.inventory.remove', [
            'removals' => $removals
        ]);
    }

    public function confirm($id)
    {
        $rq = InventoryRemovalQueue::findOrFail($id);
        if ($rq->status !== 'pending') {
            return back()->with('error', 'Already processed');
        }

        // Decrement allocation
        $alloc = InventoryAllocation::where('inventory_id', $rq->inventory_id)
            ->where('location_id', $rq->location_id)
            ->first();

        if (!$alloc || $alloc->allocated_quantity < $rq->quantity) {
            return back()->with('error', 'Not enough allocated stock');
        }
        $alloc->decrement('allocated_quantity', $rq->quantity);

        // Decrement master
        $master = InventoryMaster::findOrFail($rq->inventory_id);
        if ($master->total_quantity < $rq->quantity) {
            return back()->with('error', 'Not enough master stock');
        }
        $master->decrement('total_quantity', $rq->quantity);

        // Log transaction
        InventoryTransaction::create([
            'inventory_id' => $rq->inventory_id,
            'type'         => 'remove',
            'quantity'     => $rq->quantity,
            'reference'    => $rq->sale_reference,
            'notes'        => 'Confirmed removal',
            'created_by'   => Auth::id()
        ]);

        $rq->update(['status' => 'confirmed']);
        return back()->with('success', 'Removal confirmed');
    }

    public function cancel($id)
    {
        $rq = InventoryRemovalQueue::findOrFail($id);
        if ($rq->status !== 'pending') {
            return back()->with('error', 'Already processed');
        }
        $rq->update(['status' => 'cancelled']);
        return back()->with('success', 'Removal cancelled');
    }
}
