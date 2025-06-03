<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\FgpOrder;
use App\Models\FgpOrderDetail;
use App\Models\InventoryAllocation;
use App\Models\Location;
use App\Models\InventoryMaster;
use App\Models\FgpItem;
use Illuminate\Support\Facades\DB;

class BulkInventoryAllocationController extends Controller
{
    public function index(Request $request)
    {
        $franchiseeId = Auth::user()->active_franchisee_id;

        $deliveredOrders = FgpOrder::with(['orderDetails.item'])
            ->where('franchisee_id', $franchiseeId)
            ->where('status', 'Delivered')
            ->get();

        $locations = Location::where('franchisee_id', $franchiseeId)->get();

        return view('franchise_admin.inventory.bulk_allocation', [
            'orders' => $deliveredOrders,
            'locations' => $locations
        ]);
    }

    public function allocate(Request $request)
    {
        $request->validate([
            'allocations' => 'required|array',
            'allocations.*.fgp_order_detail_id' => 'required|integer|exists:fgp_order_details,fgp_order_detail_id',
            'allocations.*.location_id' => 'required|integer|exists:locations,location_id',
            'allocations.*.quantity' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->allocations as $allocation) {
                $detail = FgpOrderDetail::find($allocation['fgp_order_detail_id']);

                if ($detail->allocated_quantity + $allocation['quantity'] > $detail->quantity) {
                    throw new \Exception("Allocation exceeds ordered quantity.");
                }

                InventoryAllocation::create([
                    'inventory_id' => InventoryMaster::firstOrCreate([
                        'franchisee_id' => Auth::user()->active_franchisee_id,
                        'fgp_item_id' => $detail->fgp_item_id
                    ])->inventory_id,
                    'location_id' => $allocation['location_id'],
                    'cases' => $allocation['quantity']
                ]);

                $detail->increment('allocated_quantity', $allocation['quantity']);

                if ($detail->allocated_quantity === $detail->quantity) {
                    $detail->fgpOrder->update(['status' => 'Allocated']);
                }
            }
        });

        return back()->with('success', 'Inventory successfully allocated.');
    }
}
