<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
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

        $initialPopFlavors = FgpItem::whereHas('deliveries', function ($q) use ($franchiseId) {
            $q->where('franchise_id', $franchiseId);
        })->select('fgp_item_id', 'name')->get();

        $inventoryMastersForPop = InventoryMaster::where('franchisee_id', $franchiseId)
            ->whereNotNull('fgp_item_id')
            ->select('inventory_id', 'fgp_item_id', 'custom_item_name')
            ->get();

        $customItems = InventoryMaster::where('franchisee_id', $franchiseId)
            ->whereNull('fgp_item_id')
            ->select('inventory_id', 'custom_item_name')
            ->get();

        $existingAllocations = InventoryAllocation::with(['inventoryMaster', 'location'])
            ->whereHas('inventoryMaster', function ($q) use ($franchiseId) {
                $q->where('franchisee_id', $franchiseId);
            })->get()->map(function ($alloc) {
                return [
                    'inventory_id'     => $alloc->inventory_id,
                    'fgp_item_id'      => $alloc->inventoryMaster->fgp_item_id,
                    'custom_item_name' => $alloc->inventoryMaster->custom_item_name,
                    'location'         => $alloc->location->name,
                    'cases'            => $alloc->allocated_quantity
                ];
            });

        return view('franchise_admin.inventory.locations', [
            'initialPopFlavors'      => $initialPopFlavors,
            'inventoryMastersForPop' => $inventoryMastersForPop,
            'customItems'            => $customItems,
            'existingAllocations'    => $existingAllocations,
            'locations'              => Auth::user()->franchisee->locations
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
