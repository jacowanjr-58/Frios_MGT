<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FpgItem;
use App\Models\FpgOrder;
use App\Models\InventoryAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class InventoryController extends Controller
{
    public function index()
    {
        $deliveredOrders = FpgOrder::where('status', 'delivered')->get();
        $shippedOrders = FpgOrder::where('status', 'shipped')->count();
        $paidOrders = FpgOrder::where('status', 'paid')->count();
        $pendingOrders = FpgOrder::where('status', 'pending')->count();


        // $orders = FpgOrder::where('user_ID', Auth::user()->franchisee_id)
        // ->select(
        //     'user_ID',
        //     'date_transaction',
        //     \DB::raw('SUM(unit_number) as total_quantity'),
        //     \DB::raw('SUM(unit_number * unit_cost) as total_amount'),
        //     'status'
        // )
        // ->groupBy('date_transaction', 'user_ID', 'status')
        // ->orderBy('date_transaction', 'desc')
        // ->with('user') // Eager load user information
        // ->get()
        // ->map(function ($order) {
        //     $order->date_transaction = Carbon::parse($order->date_transaction);
        //     return $order;
        // });

        $orders = FpgOrder::where('user_ID' , Auth::user()->franchisee_id)->get();

    $totalOrders = $orders->count();


        return view('franchise_admin.inventory.index', compact('deliveredOrders', 'shippedOrders', 'pendingOrders','paidOrders', 'orders', 'totalOrders'));
    }

    public function inventoryDetail(Request $request)
    {
        $orderId = $request->input('id');

        $orderDetails = DB::table('fgp_order_details as od')
        ->join('fpg_items as fi', 'od.fgp_item_id', '=', 'fi.fgp_item_id')
        ->where('od.fpg_order_id', $orderId)
        ->select('od.*', 'fi.name')
        ->get();

    // Format the date_transaction for each order detail
    foreach ($orderDetails as $detail) {
        // Format the date using Carbon
        $detail->formatted_date = Carbon::parse($detail->date_transaction)->format('M d, Y h:i A');
    }


        return response()->json([
            'orderDetails' => $orderDetails,
        ]);
    }



    public function inventoryLocations()
    {
        try {
            $flavors = FpgItem::all();

            $initialPopFlavors = [];
            foreach ($flavors as $flavor) {
                $initialPopFlavors[] = [
                    'name' => $flavor->name,
                    'image1' => $flavor->image1,
                    'available' => $flavor->availableQuantity(),
                ];
            }

            $allocatedInventory = InventoryAllocation::join('fpg_items', 'fpg_items.fgp_item_id', '=', 'inventory_allocations.fpg_item_id')
                ->select('fpg_items.name as flavor', 'inventory_allocations.location', 'inventory_allocations.quantity as cases')
                ->get();

            return view('franchise_admin.inventory.locations', compact(
                'flavors',
                'initialPopFlavors',
                'allocatedInventory'
            ));
        } catch (\Exception $e) {
            // Log error or dd for debug
            dd('Error: ' . $e->getMessage());
        }
    }
    

    public function allocateInventory(Request $request)
    {
        try {
            foreach ($request->allocatedInventory as $item) {
                $fpg_item_id = FpgItem::where('name', $item['flavor'])->first()->fgp_item_id ?? null;
                if (!$fpg_item_id) {
                    continue;
                }
                $exists = InventoryAllocation::where('fpg_item_id', $fpg_item_id)->where('location', $item['location'])->first();
                if($exists){
                    $exists->update([
                    'quantity' => $item['cases'],
                    ]);
                }else{
                    // return $fpg_item_id;
                    InventoryAllocation::create([
                        'fpg_item_id' => $fpg_item_id,
                        'quantity' => $item['cases'],
                        'location' => $item['location']
                    ]);

                }
            }
            return response()->json([
                'error' => false,
                'message' => 'location allocated successfully'
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }


    public function updateQuantity(Request $request)
    {
        try {
            $fpg_item_id = FpgItem::where('name', $request->flavor)->first()->fgp_item_id ?? null;

            if (!$fpg_item_id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid flavor'
                ]);
            }

            $allocation = InventoryAllocation::where('fpg_item_id', $fpg_item_id)
                ->where('location', $request->location)
                ->first();

            if ($allocation) {
                if ($allocation->quantity <= 1) {
                    $allocation->delete();
                    return response()->json([
                        'error' => false,
                        'message' => 'Item deleted because quantity reached zero'
                    ]);
                } else {
                    $allocation->decrement('quantity');
                    return response()->json([
                        'error' => false,
                        'message' => 'Quantity decreased by 1'
                    ]);
                }
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Allocation not found'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }


    public function removeItem(Request $request)
    {
        try {
            $fpg_item_id = FpgItem::where('name', $request->flavor)->first()->fgp_item_id ?? null;

            if (!$fpg_item_id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid flavor'
                ]);
            }

            InventoryAllocation::where('fpg_item_id', $fpg_item_id)
                ->where('location', $request->location)
                ->delete();

            return response()->json([
                'error' => false,
                'message' => 'Item removed successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }

}
