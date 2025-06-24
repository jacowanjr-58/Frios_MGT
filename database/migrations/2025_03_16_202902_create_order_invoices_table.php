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
        if (! Schema::hasTable('orders_invoice')) {
        Schema::create('orders_invoice', function (Blueprint $table) {
            $table->id('invoice_ID');
            $table->unsignedBigInteger('user_ID');
            $table->unsignedBigInteger('franchise_id');
            $table->unsignedBigInteger('custom_ID')->nullable();
            $table->json('order_items_ID_list');
            $table->dateTime('date_created');
            $table->string('status');
            $table->string('payment_type');
            $table->decimal('sales_tax', 10, 2)->nullable();
            $table->timestamps();
        
            // Foreign Keys
            $table->foreign('user_ID')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('franchise_id')
                ->references('franchise_id')
                ->on('franchises')
                ->onDelete('cascade');
        });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_invoices');
    }
};
