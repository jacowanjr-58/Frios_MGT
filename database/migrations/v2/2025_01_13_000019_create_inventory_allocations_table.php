<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('inventory_allocations')) {
            Schema::create('inventory_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('franchise_id')->constrained('franchises','id');
                $table->foreignId('inventory_id')->constrained('inventories','id');
                $table->foreignId('inventory_location_id')->constrained('inventory_locations','id');
                $table->foreignId('fgp_item_id')->constrained('fgp_items','id');
                $table->string('custom_item_name')->nullable();
                $table->string('location');
                $table->string('quantity');
                $table->integer('allocated_quantity')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_allocations');
    }
};
