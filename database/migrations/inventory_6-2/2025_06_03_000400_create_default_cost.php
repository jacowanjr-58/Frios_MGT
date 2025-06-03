<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultCostToInventoryMasterTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('inventory_master', function (Blueprint $table) {
            // Add a nullable decimal column for custom‐item cost, after custom_item_name
            $table->decimal('default_cost', 8, 2)
                  ->nullable()
                  ->after('custom_item_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('inventory_master', function (Blueprint $table) {
            $table->dropColumn('default_cost');
        });
    }
}
