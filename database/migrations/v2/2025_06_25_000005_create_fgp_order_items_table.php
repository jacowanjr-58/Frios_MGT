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
        if (! Schema::hasTable('fgp_order_items')) {
        Schema::create('fgp_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fgp_order_id')->constrained('fgp_orders', 'id');
            $table->string('item'); // Can be free text or linked to inventory
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fgp_order_items');
    }
};
