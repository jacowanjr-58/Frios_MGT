<?php

namespace App\Http\Controllers\Franchise;

use App\Models\FgpItem;
use App\Models\FgpOrder;
use App\Models\Franchise;
use Illuminate\Http\Request;
use App\Models\InventoryMaster;
use App\Models\FgpOrderDiscrepancy;
use App\Models\InventoryLocation;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryAllocation;
use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;

class InventoryAllocationController extends Controller
{
    public function inventoryLocations(Franchise $franchise)
    {
        // 1) Grab all "delivered" Pop flavors from inventory_master:
        //    i.e. any master row with fgp_item_id != null AND positive on-hand.
        $initialPopFlavors = InventoryMaster::with('flavor')
            ->where('franchise_id', $franchise->id)
            ->whereNotNull('fgp_item_id')
            ->where('total_quantity', '>', 0)
            ->get(['id', 'inventory_id', 'fgp_item_id', 'custom_item_name']);
        // note: 'custom_item_name' will be null for real Pops

        // 2) Grab all "custom" inventory‐master lines (fgp_item_id IS NULL):
        $customItems = InventoryMaster::where('franchise_id', $franchise->id)
            ->whereNull('fgp_item_id')
            ->get(['id', 'inventory_id', 'custom_item_name']);

        // 3) Load existing allocations for this franchise:
        $existingAllocations = InventoryAllocation::with(['inventoryMaster.flavor', 'location'])
            ->whereHas('inventoryMaster', function ($q) use ($franchise) {
                $q->where('franchise_id', $franchise->id);
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

        // 4) Grab all locations for this franchise:
        $locations = InventoryLocation::where('franchise_id', $franchise->id)
            ->orderBy('name')
            ->get();

        return view('franchise_admin.inventory.locations', [
            'initialPopFlavors'      => $initialPopFlavors,
            'customItems'            => $customItems,
            'existingAllocations'    => $existingAllocations,
            'locations'              => $locations,
        ]);
    }




    public function allocateInventory(Request $request, Franchise $franchise)
    {
        $request->validate([
            'allocatedInventory' => 'required|array',
            'allocatedInventory.*.inventory_id' => 'required|exists:inventory_master,inventory_id',
            'allocatedInventory.*.location'     => 'required|string',
            'allocatedInventory.*.cases'        => 'required|integer|min:1',
        ]);

        // Delete existing allocations for this franchise
        $masterIds = InventoryMaster::where('franchise_id', $franchise->id)->pluck('inventory_id');
        InventoryAllocation::whereIn('inventory_id', $masterIds)->delete();

        foreach ($request->input('allocatedInventory') as $entry) {
            $locModel = InventoryLocation::where('name', $entry['location'])
                ->where('franchise_id', $franchise->id)
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


    /**
     * Show the “Confirm Delivery” form.
     *  Display a form so the franchise can confirm / adjust the delivered quantities.
     * @param  int  $order  (this is fgp_ordersID)
     */

    public function showConfirmDelivery(Franchise $franchise, FgpOrder $orders)
    {
        // 1)  make sure this franchise “owns” the order
        if ($orders->franchise_id !== $franchise->id) {
            abort(403, 'Unauthorized');
        }

        // 2) Load the order and eager-load its details
        $orders->load('orderItems', 'orderItems.item');


        return view('franchise_admin.inventory.confirm_delivery',  compact('orders', 'franchise'));
    }

    /**
     * Process the “Confirm Delivery” form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId   (the fgp_ordersID from the URL)
     */

    public function postConfirmDelivery(Request $request, Franchise $franchise, FgpOrder $order)
    {
        // 1)  make sure this franchise “owns” the order
        if ($order->franchise_id !== $franchise->id) {
            abort(403, 'Unauthorized');
        }

        // 2) Load the order and eager-load its details
        $order->load('orderDetails', 'orderDetails.item');

        // 3) Build & run validation
        $rules = [];
        foreach ($order->orderDetails as $detail) {
            $rules["received_qty.{$detail->id}"] = 'required|integer|min:0';
            $rules["damaged_units.{$detail->id}"] = 'nullable|integer|min:0';
        }
        $validated = $request->validate($rules);

        // 4) Process each detail inside a transaction
        DB::beginTransaction();
        try {
            foreach ($order->orderDetails as $detail) {


                // a) Sync InventoryMaster
                $itemId      = $detail->fgp_item_id;
                $franchiseId = $order->franchise_id;

                // Corporate defaults
                $fItem       = FgpItem::findOrFail($itemId);
                $splitFactor = (int) $fItem->split_factor;
                $caseCost    = (float) $fItem->case_cost;

                // Load quantities from validated data
                // Note: received_qty is the number of cases received, not units
                $receivedQty = (int) $validated['received_qty'][$detail->id];
                $damagedQty  = (int) ($validated['damaged_units'][$detail->id] ?? 0);
                $orderedQty  = (int) $detail->unit_number * $splitFactor; // number of cases ordered * $splitFactor (gives Units)

                // Fetch or create inventory_master row
                $inventory = InventoryMaster::firstOrCreate(
                    [
                        'franchise_id' => $franchiseId,
                        'fgp_item_id'   => $itemId,
                    ],
                    [
                        'total_quantity' => 0,
                        'split_factor'   => $splitFactor,
                        'cogs_case'      => $caseCost,
                    ]
                );

                // Always keep split & cost in sync
                $inventory->split_factor   = $splitFactor;
                $inventory->cogs_case      = $caseCost;

                // Increment by total quantity received → units;
                $inventory->total_quantity += ($receivedQty * $splitFactor) - $damagedQty;

                $inventory->save();

                // b) FUTURE Log an InventoryTransaction if desired
                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id, // PK in inventory_master
                    'type'         => 'order_add',
                    'quantity'     => $inventory->total_quantity,
                    'reference'    => 'Order #' . $order->id,
                    'notes'        => 'Item ID: ' . $itemId,
                    'created_by'   => Auth::user()->user_id,
                ]);

                // c) Discrepancy if cases received ≠ cases ordered
                if ($receivedQty !== $orderedQty) {
                    FgpOrderDiscrepancy::create([
                        'order_id'           => $order->id,
                        'order_detail_id'    => $detail->id,
                        'quantity_ordered'   => $orderedQty,
                        'quantity_received'  => $receivedQty,
                        'notes'              => $request->input("notes.{$detail->id}", null),
                    ]);
                }
            }
            $order->status       = 'Delivered';
            $order->is_delivered = true;
            $order->delivered_at = now();
            $order->save();

            // (Optional) Notify Corporate if there were any discrepancies
            // if ($order->orderDiscrepancies()->count() > 0) {
            //     \Notification::route('mail', 'corporate@friospops.com')
            //         ->notify(new \App\Notifications\FgpOrderDiscrepancyNotification($order));
            // }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Error confirming delivery: ' . $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('franchise.orderpops.view')
            ->with('success', 'Delivery confirmed and inventory updated.');
    }
}
