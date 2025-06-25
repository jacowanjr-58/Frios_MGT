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
        Schema::create('ups_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fgp_order_id');
            $table->foreign('fgp_order_id')->references('id')->on('fgp_orders')->onDelete('cascade');
            $table->string('shipment_id')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('label_format')->default('PDF');
            $table->string('label_file_path')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ups_shipments');
    }
};
