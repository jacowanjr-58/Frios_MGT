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

    // Ensure no invalid data before applying foreign keys
    DB::table('inventory_allocations')
        ->whereNotNull('inventory_id')
        ->whereNotIn('inventory_id', function ($query) {
            $query->select('inventory_id')->from('inventory_master');
        })
        ->delete();

    DB::table('inventory_allocations')
        ->whereNotNull('location_id')
        ->whereNotIn('location_id', function ($query) {
            $query->select('locations_ID')->from('locations');
        })
        ->delete();

    // ---- DROP foreign keys if they exist ----
    Schema::table('inventory_allocations', function (Blueprint $table) {
        // Use try/catch to avoid errors if the foreign key doesn't exist
        try {
            $table->dropForeign(['inventory_id']);
        } catch (\Exception $e) {}
        try {
            $table->dropForeign(['location_id']);
        } catch (\Exception $e) {}
    });

    // ---- ADD foreign keys ----
    Schema::table('inventory_allocations', function (Blueprint $table) {
        $table->foreign('inventory_id')
            ->references('inventory_id')
            ->on('inventory_master')
            ->onDelete('cascade');

        $table->foreign('location_id')
            ->references('locations_ID')
            ->on('locations')
            ->onDelete('cascade');
    });
}
};
