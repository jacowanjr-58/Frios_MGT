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
        Schema::table('inventory_allocations', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_allocations', 'inventory_id')) {
                $table->unsignedBigInteger('inventory_id')->after('id');
            }

            if (!Schema::hasColumn('inventory_allocations', 'location_id')) {
                $table->unsignedBigInteger('location_id')->after('inventory_id');
            }

            if (!Schema::hasColumn('inventory_allocations', 'allocated_quantity')) {
                $table->integer('allocated_quantity')->after('location_id');
            }

            if (!Schema::hasColumns('inventory_allocations', ['created_at', 'updated_at'])) {
                $table->timestamps();
            }
        });

        // Separate block for foreign keys (after columns are created)
        Schema::table('inventory_allocations', function (Blueprint $table) {
            // Check if foreign keys don't already exist (optional check for robustness)
            try {
                $table->foreign('inventory_id')
                    ->references('inventory_id')
                    ->on('inventory_master')
                    ->onDelete('cascade');

                $table->foreign('location_id')
                    ->references('locations_ID')
                    ->on('locations')
                    ->onDelete('cascade');
            } catch (\Illuminate\Database\QueryException $e) {
                // Log or report the error if needed
                info("Foreign key creation failed: " . $e->getMessage());
            }
        });
    }

};
