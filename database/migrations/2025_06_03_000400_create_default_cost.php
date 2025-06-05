<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up():void
    {
        if (Schema::hasTable('inventory_master') && ! Schema::hasColumn('inventory_master', 'default_cost')) {
        Schema::table('inventory_master', function (Blueprint $table) {
            // Add a nullable decimal column for custom‐item cost, after custom_item_name
            $table->decimal('default_cost', 8, 2)
                  ->nullable()
                  ->after('custom_item_name');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down():void
    {
        if (Schema::hasTable('inventory_master')) {
        Schema::table('inventory_master', function (Blueprint $table) {
            $table->dropColumn('default_cost');
        });
        }
    }
};
