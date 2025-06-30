<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('inventories')) {
            Schema::create('inventories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('franchise_id')->constrained('franchises', 'id');
                $table->foreignId('fgp_item_id')->constrained('fgp_items', 'id');
                $table->foreignId('inventory_location_id')->nullable()->constrained('inventory_locations', 'id');
                $table->integer('stock_on_hand');
                $table->dateTime('stock_count_date')->nullable();
                $table->integer('pops_on_hand')->nullable();
                $table->decimal('whole_sale_price_case', 10, 2)->nullable();
                $table->decimal('retail_price_pop', 10, 2)->nullable();
                $table->foreignId('created_by')->constrained('users', 'id');
                $table->foreignId('updated_by')->constrained('users', 'id');
                $table->timestamps();
            });
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
