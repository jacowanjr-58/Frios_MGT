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
        if (! Schema::hasTable('payments')) {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payments_ID');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->json('items'); // List of items paid for
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->decimal('taxes', 10, 2)->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('order_id')->references('fgp_ordersID')->on('fgp_orders')->onDelete("cascade");
        });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
