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
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises', 'id');
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'id');
            $table->foreignId('fgp_order_id')->nullable()->constrained('fgp_orders', 'id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // e.g., Cash, Credit Card, etc.
            $table->timestamps();

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
