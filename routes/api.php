<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\ShipStationWebhookController;

Route::post('/webhooks/shipstation/{token}', function ($token, \Illuminate\Http\Request $request) {
    abort_unless(
        $token === config('services.shipstation.webhook_token'),
        403,
        'Invalid webhook token'
    );

    return app(ShipStationWebhookController::class)->handle($request);
});
