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
            $table->unsignedBigInteger('user_ID');
            $table->dateTime('date_transaction');
            $table->json('ACH_data')->nullable();
            $table->enum('status', ['Pending', 'Paid','Shipped','Delivered']);
            $table->timestamps();

            $table->foreign('user_ID')->references('user_id')->on('users')->onDelete('cascade');
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
    