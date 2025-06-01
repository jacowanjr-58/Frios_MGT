<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        Schema::table('fgp_orders', function (Blueprint $table) {
            $table->string('shipstation_order_id')->nullable()->after('tracking_number');
            $table->text('shipstation_label_url')->nullable()->after('shipstation_order_id');
            $table->longText('shipstation_raw_response')->nullable()->after('shipstation_label_url');
            $table->timestamp('label_created_at')->nullable()->after('delivered_at');
            $table->string('shipstation_webhook_event')->nullable()->after('label_created_at');
        });
    }

    public function down(): void
    {
        Schema::table('fgp_orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipstation_order_id',
                'shipstation_label_url',
                'shipstation_raw_response',
                'label_created_at',
                'shipstation_webhook_event',
            ]);
        });
    }
};
