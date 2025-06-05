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
        if (! Schema::hasTable('inventory')) {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->unsignedBigInteger('franchisee_id');
            $table->unsignedBigInteger('fgp_item_id');
            $table->integer('stock_on_hand');
            $table->dateTime('stock_count_date')->nullable();
            $table->unsignedBigInteger('locations_ID')->nullable();
            $table->integer('pops_on_hand')->nullable();
            $table->decimal('whole_sale_price_case', 10, 2)->nullable();
            $table->decimal('retail_price_pop', 10, 2)->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('franchisee_id')->references('franchisee_id')->on('franchisees')->onDelete('cascade');
            $table->foreign('fgp_item_id')->references('fgp_item_id')->on('fgp_items')->onDelete('cascade');
            $table->foreign('locations_ID')->references('locations_ID')->on('locations')->onDelete('set null');
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
