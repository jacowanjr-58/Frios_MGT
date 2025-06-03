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
        Schema::table('inventory_allocations', function (Blueprint $table) {
            // 1) Make fgp_item_id nullable
            $table->unsignedBigInteger('fgp_item_id')
                  ->nullable()
                  ->change();

            // 2) Add custom_item_name
            $table->string('custom_item_name')
                  ->nullable()
                  ->after('fgp_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('inventory_allocations', function (Blueprint $table) {
            // Remove custom_item_name
            $table->dropColumn('custom_item_name');

            // Revert fgp_item_id to NOT NULL
            $table->unsignedBigInteger('fgp_item_id')
                  ->nullable(false)
                  ->change();
        });
    }
};
