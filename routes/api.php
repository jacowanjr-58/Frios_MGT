<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhooks\ShipStationWebhookController;

Route::middleware('api')->group(function () {
    Route::post('/webhooks/shipstation', [ShipStationWebhookController::class, 'handle']);
});
