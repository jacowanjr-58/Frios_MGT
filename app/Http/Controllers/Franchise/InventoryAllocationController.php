<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FgpItem;
use App\Models\InventoryMaster;
use App\Models\InventoryAllocation;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryAllocationController extends Controller
{
    public function inventoryLocations()
    {
        $franchiseId = Auth::user()->franchisee_id;

         // 1) Grab all "delivered" Pop flavors from inventory_master:
        //    i.e. any master row with fgp_item_id != null AND positive on-hand.
        $initialPopFlavors = InventoryMaster::with('flavor')
            ->where('franchisee_id', $franchiseId)
            ->whereNotNull('fgp_item_id')
            ->where('total_quantity', '>', 0)
            ->get(['inventory_id', 'fgp_item_id', 'custom_item_name']);
            // note: 'custom_item_name' will be null for real Pops

        // 2) Grab all "custom" inventory‐master lines (fgp_item_id IS NULL):
        $customItems = InventoryMaster::where('franchisee_id', $franchiseId)
            ->whereNull('fgp_item_id')
            ->get(['inventory_id', 'custom_item_name']);

        // 3) Load existing allocations for this franchisee:
        $existingAllocations = InventoryAllocation::with(['inventoryMaster.flavor', 'location'])
            ->whereHas('inventoryMaster', function ($q) use ($franchiseId) {
                $q->where('franchisee_id', $franchiseId);
            })
            ->get()
            ->map(function ($alloc) {
                return [
                    'inventory_id'     => $alloc->inventory_id,
                    'fgp_item_id'      => $alloc->inventoryMaster->fgp_item_id,
                    'custom_item_name' => $alloc->inventoryMaster->custom_item_name,
                    'location'         => $alloc->location->name,
                    'cases'            => $alloc->allocated_quantity,
                ];
            });

        // 4) Grab all locations for this franchisee:
        $locations = Location::where('franchisee_id', $franchiseId)
                    ->orderBy('name')
                    ->get();

        return view('franchise_admin.inventory.locations', [
            'initialPopFlavors'      => $initialPopFlavors,
            'customItems'            => $customItems,
            'existingAllocations'    => $existingAllocations,
            'locations'              => $locations,
        ]);
    }




    public function allocateInventory(Request $request)
    {
        $request->validate([
            'allocatedInventory' => 'required|array',
            'allocatedInventory.*.inventory_id' => 'required|exists:inventory_master,inventory_id',
            'allocatedInventory.*.location'     => 'required|string',
            'allocatedInventory.*.cases'        => 'required|integer|min:1',
        ]);

        $franchiseId = Auth::user()->franchisee_id;

        // Delete existing allocations for this franchise
        $masterIds = InventoryMaster::where('franchisee_id', $franchiseId)->pluck('inventory_id');
        InventoryAllocation::whereIn('inventory_id', $masterIds)->delete();

        foreach ($request->input('allocatedInventory') as $entry) {
            $locModel = Location::where('name', $entry['location'])
                ->where('franchisee_id', $franchiseId)
                ->first();

            if (!$locModel) {
                continue;
            }

            InventoryAllocation::create([
                'inventory_id'        => $entry['inventory_id'],
                'location_id'         => $locModel->locations_ID,
                'allocated_quantity'  => $entry['cases'],
            ]);
        }

        return response()->json(['error' => false, 'message' => 'Allocation saved']);
    }
}
