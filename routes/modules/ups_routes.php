<?php



use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;



Route::middleware('auth')->group(function () {

        Route::get('/ups/authenticate', function () {
            $ups = new \App\Services\UPS\UPSShippingService();
            return "Authenticated with token: {$ups->token}";
        });


        Route::get('/order/{orderId}/create-ups-label', [\App\Http\Controllers\Shipping\UPSShippingController::class, 'createLabel']);

       /*  Route::get('/order/{fgp_ordersID}/create-ups-label', function ($fgp_ordersID) {
            $order = \App\Models\FgpOrder::with('orderDetails')->findOrFail($fgp_ordersID);

            $ups = new \App\Services\UPS\UPSShippingService();
            $tracking = $ups->createShipment($order);

            return "Label created with tracking #{$tracking}";
        }); */

});
