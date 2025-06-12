<?php
namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Models\FgpOrder;
use App\Services\UPS\UPSShippingService;
use Illuminate\Http\Request;

class UPSShippingController extends Controller
{
    public function createLabel($fgp_ordersID)
    {
        try {
            $order = FgpOrder::with('orderDetails')->findOrFail($fgp_ordersID);

            $ups = new UPSShippingService();
            $tracking = $ups->createShipment($order);

            return response()->json([
                'success' => true,
                'tracking_number' => $tracking,
                'message' => "Label created with tracking #{$tracking}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create UPS label: ' . $e->getMessage()
            ], 500);
        }
    }
}
