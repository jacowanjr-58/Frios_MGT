<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FgpItem;
use App\Models\InventoryMaster;
use App\Models\User;
use App\Models\InventoryAllocation;
use App\Models\InventoryTransaction;
use App\Models\FgpOrder;
use App\Models\FgpOrderDetail;
use App\Models\OrderDiscrepancy;
use App\Models\InventoryLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $locations = InventoryLocation::where('franchisee_id', $franchiseId)
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
            $locModel = InventoryLocation::where('name', $entry['location'])
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


    /**
     * Show the “Confirm Delivery” form.
     *  Display a form so the franchisee can confirm / adjust the delivered quantities.
     * @param  int  $order  (this is fgp_ordersID)
     */

    public function showConfirmDelivery($order)
    {
        // 1) Load the order and eager-load its details
        $pass_order = FgpOrder::with('orderDetails.item')   // assuming you have a relation "orderDetails" → FgpOrderDetail, and each detail has an "item"
                        ->findOrFail($order);

        // 2)  make sure this franchise “owns” the order
        if (Auth::user()->franchisee_id !== $pass_order->franchisee_id) {
            abort(403, 'Unauthorized');
        }

        return view('franchise_admin.inventory.confirm_delivery',  [ 'order' => $pass_order ]);
    }

    /**
     * Process the “Confirm Delivery” form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId   (the fgp_ordersID from the URL)
     */

   public function postConfirmDelivery(Request $request, $orderId)
{
    // 1) Load the order + details
    $order = FgpOrder::with('orderDetails')
                ->where('fgp_ordersID', $orderId)
                ->firstOrFail();

    // 2) Authorization
    if (Auth::user()->franchisee_id !== $order->franchisee_id) {
        abort(403, 'Unauthorized');
    }

    // 3) Build & run validation
    $rules = [];
    foreach ($order->orderDetails as $detail) {
        $rules["received_qty.{$detail->id}"] = 'required|integer|min:0';
    }
    $validated = $request->validate($rules);

    // 4) Process each detail inside a transaction
    DB::beginTransaction();
    try {
        foreach ($order->orderDetails as $detail) {
            $receivedQty = (int) $validated['received_qty'][$detail->id];
            $orderedQty  = (int) $detail->unit_number; // number of cases ordered

            // Skip nothing received
            if ($receivedQty <= 0) {
                continue;
            }

            // a) Sync InventoryMaster
            $itemId      = $detail->fgp_item_id;
            $franchiseId = $order->franchisee_id;

            // Corporate defaults
            $fItem       = FgpItem::findOrFail($itemId);
            $splitFactor = (int) $fItem->split_factor;
            $caseCost    = (float) $fItem->case_cost;

            // Fetch or create inventory_master row
            $inventory = InventoryMaster::firstOrCreate(
                [
                    'franchisee_id' => $franchiseId,
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

            // Increment by received cases → units
            $inventory->total_quantity += $receivedQty * $splitFactor;

            $inventory->save();

           /*  // b) FUTURE Log an InventoryTransaction if desired
            InventoryTransaction::create([
                'franchisee_id'           => $franchiseId,
                'inventory_id'            => $inventory->inventory_id,
                'event_id'                => null,
                'cardholder_name'         => Auth::user()->name,
                'amount'                  => $receivedQty * $caseCost,
                'stripe_payment_intent_id'=> null,
                'stripe_payment_method'   => null,
                'stripe_currency'         => null,
                'stripe_status'           => 'received',
                'created_at'              => now(),
                'updated_at'              => now(),
            ]); */

            // c) Discrepancy if cases received ≠ cases ordered
            if ($receivedQty !== $orderedQty) {
                OrderDiscrepancy::create([
                    'order_id'           => $order->fgp_ordersID,
                    'order_detail_id'    => $detail->id,
                    'quantity_ordered'   => $orderedQty,
                    'quantity_received'  => $receivedQty,
                    'notes'              => $request->input("notes.{$detail->id}", null),
                ]);
            }
        }

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()
            ->withErrors(['error' => 'Error confirming delivery: '.$e->getMessage()])
            ->withInput();
    }

    return redirect()
        ->route('franchise.orderpops.view')
        ->with('success', 'Delivery confirmed and inventory updated.');
}
}
