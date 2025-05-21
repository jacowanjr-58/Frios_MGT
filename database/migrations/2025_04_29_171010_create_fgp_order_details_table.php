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
        Schema::create('fgp_order_details', function (Blueprint $table) {
            $table->id();
            $table->integer('fgp_order_id');
            $table->integer('fgp_item_id');
            $table->decimal('unit_cost', 10, 2);
            $table->integer('unit_number');
            $table->dateTime('date_transaction')->nullable();
            $table->json('ACH_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fgp_order_details');
    }
};
