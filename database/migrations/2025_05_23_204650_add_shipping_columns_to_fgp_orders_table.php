<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fgp_orders', function (Blueprint $table) {
            $table->string('ship_to_name')->nullable();
            $table->string('ship_to_address1')->nullable();
            $table->string('ship_to_address2')->nullable();
            $table->string('ship_to_city', 100)->nullable();
            $table->string('ship_to_state', 100)->nullable();
            $table->string('ship_to_zip', 20)->nullable();
            $table->string('ship_to_country', 100)->nullable();
            $table->string('ship_to_phone', 50)->nullable();
            $table->string('ship_method', 100)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('shipstation_order_id')->nullable();
            $table->text('shipstation_label_url')->nullable();
            $table->longText('shipstation_raw_response')->nullable();
            $table->boolean('is_paid')->default(0);
            $table->enum('shipstation_status', [
                'awaiting_payment',
                'awaiting_shipment',
                'shipped',
                'on_hold',
                'cancelled',
                'pending_fulfillment'
            ])->default('awaiting_payment')->nullable();
            $table->boolean('is_delivered')->default(0);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('label_created_at')->nullable();
            $table->string('shipstation_webhook_event')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fgp_orders', function (Blueprint $table) {
            $table->dropColumn([
                'ship_to_name',
                'ship_to_address1',
                'ship_to_address2',
                'ship_to_city',
                'ship_to_state',
                'ship_to_zip',
                'ship_to_country',
                'ship_to_phone',
                'ship_method',
                'tracking_number',
                'shipstation_order_id',
                'shipstation_label_url',
                'shipstation_raw_response',
                'is_paid',
                'shipstation_status',
                'is_delivered',
                'delivered_at',
                'label_created_at',
                'shipstation_webhook_event'
            ]);
        });
    }
};
