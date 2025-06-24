<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks to avoid constraint issues during renaming
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Rename users.user_id to users.id
        if (Schema::hasColumn('users', 'user_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('user_id', 'id');
            });
        }

        // 2. Rename franchises.franchise_id to franchises.id
        if (Schema::hasColumn('franchises', 'franchise_id')) {
            Schema::table('franchises', function (Blueprint $table) {
                $table->renameColumn('franchise_id', 'id');
            });
        }

        // 3. Rename customers.customer_id to customers.id
        if (Schema::hasColumn('customers', 'customer_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->renameColumn('customer_id', 'id');
            });
        }

        // 4. Rename fgp_orders.fgp_ordersID to fgp_orders.id
        if (Schema::hasColumn('fgp_orders', 'fgp_ordersID')) {
            Schema::table('fgp_orders', function (Blueprint $table) {
                $table->renameColumn('fgp_ordersID', 'id');
            });
        }

        if (Schema::hasColumn('fgp_orders', 'user_ID')) {
            Schema::table('fgp_orders', function (Blueprint $table) {
                $table->renameColumn('user_ID', 'user_id');
            });
        }

        // 5. Rename fgp_items.fgp_item_id to fgp_items.id
        if (Schema::hasColumn('fgp_items', 'fgp_item_id')) {
            Schema::table('fgp_items', function (Blueprint $table) {
                $table->renameColumn('fgp_item_id', 'id');
            });
        }

        // 6. Rename fgp_categories.category_ID to fgp_categories.id
        if (Schema::hasColumn('fgp_categories', 'category_ID')) {
            Schema::table('fgp_categories', function (Blueprint $table) {
                $table->renameColumn('category_ID', 'id');
            });
        }

        // 7. Rename inventory_master.inventory_id to inventory_master.id
        if (Schema::hasColumn('inventory_master', 'inventory_id')) {
            Schema::table('inventory_master', function (Blueprint $table) {
                $table->renameColumn('inventory_id', 'id');
            });
        }

        // 8. Rename inventory_transactions.transaction_id to inventory_transactions.id
        if (Schema::hasColumn('inventory_transactions', 'transaction_id')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->renameColumn('transaction_id', 'id');
            });
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Reverse the column renames
        if (Schema::hasColumn('users', 'id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('id', 'user_id');
            });
        }

        if (Schema::hasColumn('franchises', 'id')) {
            Schema::table('franchises', function (Blueprint $table) {
                $table->renameColumn('id', 'franchise_id');
            });
        }

        if (Schema::hasColumn('customers', 'id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->renameColumn('id', 'customer_id');
            });
        }

        if (Schema::hasColumn('fgp_orders', 'id')) {
            Schema::table('fgp_orders', function (Blueprint $table) {
                $table->renameColumn('id', 'fgp_ordersID');
            });
        }

        if (Schema::hasColumn('fgp_items', 'id')) {
            Schema::table('fgp_items', function (Blueprint $table) {
                $table->renameColumn('id', 'fgp_item_id');
            });
        }

        if (Schema::hasColumn('fgp_categories', 'id')) {
            Schema::table('fgp_categories', function (Blueprint $table) {
                $table->renameColumn('id', 'category_ID');
            });
        }

        if (Schema::hasColumn('inventory_master', 'id')) {
            Schema::table('inventory_master', function (Blueprint $table) {
                $table->renameColumn('id', 'inventory_id');
            });
        }

        if (Schema::hasColumn('inventory_transactions', 'id')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->renameColumn('id', 'transaction_id');
            });
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}; 