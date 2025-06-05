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
        if (! Schema::hasTable('sales')) {
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sales_id');
            $table->unsignedBigInteger('franchisee_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // e.g., Cash, Credit Card, etc.
            $table->dateTime('date');
            $table->timestamps();
        
            // Foreign Keys
            $table->foreign('franchisee_id')->references('franchisee_id')->on('franchisees')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('set null');
        });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
