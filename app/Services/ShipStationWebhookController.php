<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShipStationWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log the webhook payload for debugging
        Log::info('ShipStation Webhook Received', ['payload' => $request->all()]);

        // Need to add code to update Order status and return new shipment info
        // Example: update order status, save shipment info, etc.

        return response()->json(['status' => 'ok']);
    }
}
