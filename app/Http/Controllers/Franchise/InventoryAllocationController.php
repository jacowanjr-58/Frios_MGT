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
        // 1) Reload the order + all its details
        $order = FgpOrder::with('orderDetails')->where('fgp_ordersID', $orderId)->firstOrFail();

        // 2) Only the franchise that “owns” this order can confirm it
        if (Auth::user()->franchisee_id !== $order->franchisee_id) {
            abort(403, 'Unauthorized');
        }

        // 3) Dynamically build validation rules:
        $rules = [];
        foreach ($order->orderDetails as $detail) {
            // Expect “received_qty[<detailId>]” for each detail
            $rules["received_qty.{$detail->id}"] = 'required|integer|min:0';
        }
        $validated = $request->validate($rules);


         // 4) Start transaction (so we either save all inventory updates or none)
        DB::beginTransaction();
        try {
            foreach ($order->orderDetails as $detail) {
                $receivedQty  = (int) $validated['received_qty'][$detail->id];
                $orderedQty   = (int) $detail->unit_number;      // how many were ordered
                $itemId       = $detail->fgp_item_id;             // FK → FgpItem
                $franchiseId  = $order->franchisee_id;
                $case_cost    = $detail->case_cost;           // which franchise

                // a) If the franchise actually received > 0, update (or create) InventoryMaster
                if ($receivedQty > 0) {
                    $inventory = InventoryMaster::firstOrCreate(
                        [
                            'franchisee_id' => $franchiseId,
                            'fgp_item_id'   => $itemId,
                            'split_factor'  => 48,
                            'cogs_case' => $case_cost,
                        ],
                        [
                            // default columns if newly created
                            'total_quantity' => 0,
                        ]
                    );

                    // Increment by exactly how many came in
                    $inventory->total_quantity += $receivedQty;
                    $inventory->save();

                    // Record an audit‐log transaction
                    InventoryTransaction::create([
                        'inventory_id' => $inventory->inventory_id, // PK in inventory_master
                        'type'         => 'order_add',
                        'quantity'     => $receivedQty,
                        'reference'    => 'Order #' . $order->fgp_ordersID,
                        'notes'        => 'Item ID: ' . $itemId,
                        'created_by'   => Auth::user()->user_id,
                    ]);
                }

                // b) If received ≠ ordered, log a discrepancy
                if ($receivedQty !== $orderedQty) {
                    OrderDiscrepancy::create([
                        'order_id'          => $order->fgp_ordersID,
                        'order_detail_id'   => $detail->id,
                        'quantity_ordered'  => $orderedQty,
                        'quantity_received' => $receivedQty,
                        'notes'             => $request->input("notes.{$detail->id}", null),
                    ]);
                }

                // c) Update the FgpOrderDetail→quantity_received column
                $detail->quantity_received = $receivedQty;
                $detail->save();
            }

            // 5) After looping through all details, mark the parent FgpOrder as “Delivered”
            $order->status       = 'Delivered';
            $order->is_delivered = true;
            $order->delivered_at = now();
            $order->save();

            // (Optional) Notify Corporate if there were any discrepancies
            // if ($order->orderDiscrepancies()->count() > 0) {
            //     \Notification::route('mail', 'corporate@friospops.com')
            //         ->notify(new \App\Notifications\OrderDiscrepancyNotification($order));
            // }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e);
            return back()
                ->withErrors(['error' => 'There was a problem confirming delivery: ' . $e->getMessage()])
                ->withInput();

        }

        return redirect()
            ->route('franchise.orderpops.view') // adjust to whatever listing page you want
            ->with('success', 'Delivery confirmed and inventory updated.');
    }

}
