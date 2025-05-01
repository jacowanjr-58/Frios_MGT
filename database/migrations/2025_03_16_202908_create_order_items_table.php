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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_items_ID');
            $table->unsignedBigInteger('invoice_ID');
            $table->string('item'); // Can be free text or linked to inventory
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        
            // Foreign Key
            $table->foreign('invoice_ID')->references('invoice_ID')->on('orders_invoice')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
