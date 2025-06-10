<?php
// filepath: routes/api.php
use App\Http\Controllers\Services\ShipStationWebhookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::post('/shipstation/webhook', [ShipStationWebhookController::class, 'handle']);
