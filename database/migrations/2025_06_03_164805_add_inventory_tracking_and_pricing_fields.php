<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventoryTrackingAndPricingFields extends Migration
{
    public function up()
    {
        Schema::table('fgp_items', function (Blueprint $table) {
            $table->string('sku')->after('fgp_item_id');
            $table->boolean('is_corporate')->default(true)->after('sku');
            $table->decimal('cost_of_goods', 10, 2)->nullable()->after('description');
            $table->decimal('wholesale_price', 10, 2)->nullable()->after('cost_of_goods');
            $table->decimal('retail_price', 10, 2)->nullable()->after('wholesale_price');
            $table->decimal('split_retail_price', 10, 2)->nullable()->after('retail_price');
            $table->integer('split_factor')->default(1)->after('split_retail_price'); // e.g. 48 pops per case
        });

        Schema::table('inventory_master', function (Blueprint $table) {
            $table->string('sku')->after('inventory_id');
            $table->integer('split_total_quantity')->default(0)->after('total_quantity'); // manually editable or derived
            $table->index(['sku', 'franchisee_id'], 'idx_inventory_sku_quantities');
        });
    }

    public function down()
    {
        Schema::table('fgp_items', function (Blueprint $table) {
            $table->dropColumn([
                'sku',
                'is_corporate',
                'cost_of_goods',
                'wholesale_price',
                'retail_price',
                'split_retail_price',
                'split_factor',
            ]);
        });

        Schema::table('inventory_master', function (Blueprint $table) {
            $table->dropColumn([
                'sku',

                'split_total_quantity',

            ]);
        });
    }
}
