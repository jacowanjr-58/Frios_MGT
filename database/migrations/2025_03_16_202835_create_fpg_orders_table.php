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
        Schema::create('fpg_orders', function (Blueprint $table) {
            $table->id('fgp_ordersID');
            $table->integer('user_ID');
            $table->integer('customer_id')->nullable();
            $table->dateTime('date_transaction');
            $table->json('ACH_data')->nullable();
            $table->enum('status', ['Pending', 'Paid','Shipped','Delivered']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fpg_orders');
    }
};
