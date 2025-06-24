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

        // 1. Rename the main table: franchisees → franchises
        if (Schema::hasTable('franchisees') && !Schema::hasTable('franchises')) {
            Schema::rename('franchisees', 'franchises');
        }

        // 2. Update all foreign key column references: franchisee_id → franchise_id
        $tables = [
            'customers' => 'franchise_id',
            'fgp_orders' => 'franchise_id',
            'fgp_items' => 'franchise_id',
            'fgp_categories' => 'franchise_id',
            'inventory_master' => 'franchise_id',
            'locations' => 'franchise_id',
            'inventories' => 'franchise_id',
            'order_invoices' => 'franchise_id',
            'events' => 'franchise_id',
            'sales' => 'franchise_id',
            'user_franchises' => 'franchise_id',
            'additional_charges' => 'franchise_id'
        ];

        foreach ($tables as $table => $column) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    try {
                        $table->renameColumn($column, 'franchise_id');
                    } catch (\Exception $e) {
                        // Column might not exist or already be renamed
                    }
                });
            }
        }

        // 3. Handle the special case of franchises table self-reference
        if (Schema::hasColumn('franchises', 'parent_franchise_id')) {
            // This column is already correctly named, just update foreign key if needed
            Schema::table('franchises', function (Blueprint $table) {
                try {
                    $table->dropForeign(['parent_franchise_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
            });
        }

        // 4. Drop existing foreign key constraints first (with comprehensive name checking)
        $tablesToDropConstraints = [
            'customers', 'fgp_orders', 'fgp_items', 'fgp_categories',
            'inventory_master', 'locations', 'inventories', 'order_invoices',
            'events', 'sales', 'user_franchises', 'additional_charges'
        ];

        foreach ($tablesToDropConstraints as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'franchise_id')) {
                // Get all foreign key constraints for this table
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = '{$tableName}' 
                    AND COLUMN_NAME = 'franchise_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                foreach ($constraints as $constraint) {
                    try {
                        DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                    } catch (\Exception $e) {
                        // Constraint might not exist, continue
                    }
                }
            }
        }

        // 5. Recreate all foreign key constraints with new table and column names
        $foreignKeys = [
            'customers' => ['franchise_id' => 'franchises.franchise_id'],
            'fgp_orders' => ['franchise_id' => 'franchises.franchise_id'],
            'fgp_items' => ['franchise_id' => 'franchises.franchise_id'],
            'fgp_categories' => ['franchise_id' => 'franchises.franchise_id'],
            'inventory_master' => ['franchise_id' => 'franchises.franchise_id'],
            'locations' => ['franchise_id' => 'franchises.franchise_id'],
            'inventories' => ['franchise_id' => 'franchises.franchise_id'],
            'order_invoices' => ['franchise_id' => 'franchises.franchise_id'],
            'events' => ['franchise_id' => 'franchises.franchise_id'],
            'sales' => ['franchise_id' => 'franchises.franchise_id'],
            'user_franchises' => ['franchise_id' => 'franchises.franchise_id'],
            'additional_charges' => ['franchise_id' => 'franchises.franchise_id'],
            'franchises' => ['parent_franchise_id' => 'franchises.franchise_id'], // Self-reference
        ];

        foreach ($foreignKeys as $table => $constraints) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($constraints) {
                    foreach ($constraints as $column => $reference) {
                        list($referenceTable, $referenceColumn) = explode('.', $reference);
                        
                        // Only add if the reference table and column exist
                        if (Schema::hasTable($referenceTable) && Schema::hasColumn($referenceTable, $referenceColumn)) {
                            try {
                                $table->foreign($column)
                                    ->references($referenceColumn)
                                    ->on($referenceTable)
                                    ->onDelete('cascade');
                            } catch (\Exception $e) {
                                // Foreign key might already exist or reference table doesn't exist
                            }
                        }
                    }
                });
            }
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

        // Drop all foreign key constraints first
        $tablesToRevert = [
            'customers', 'fgp_orders', 'fgp_items', 'fgp_categories',
            'inventory_master', 'locations', 'inventories', 'order_invoices',
            'events', 'sales', 'user_franchises', 'additional_charges', 'franchises'
        ];

        foreach ($tablesToRevert as $tableName) {
            if (Schema::hasTable($tableName)) {
                // Get all foreign key constraints for this table
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = '{$tableName}' 
                    AND COLUMN_NAME = 'franchise_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                foreach ($constraints as $constraint) {
                    try {
                        DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                    } catch (\Exception $e) {
                        // Constraint might not exist, continue
                    }
                }
            }
        }

        // Revert column renames
        $columnsToRevert = [
            'customers' => 'franchise_id',
            'fgp_orders' => 'franchise_id',
            'fgp_items' => 'franchise_id',
            'fgp_categories' => 'franchise_id',
            'inventory_master' => 'franchise_id',
            'locations' => 'franchise_id',
            'inventories' => 'franchise_id',
            'order_invoices' => 'franchise_id',
            'events' => 'franchise_id',
            'sales' => 'franchise_id',
            'user_franchises' => 'franchise_id',
            'additional_charges' => 'franchise_id',
        ];

        foreach ($columnsToRevert as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $oldColumn = str_replace('franchise', 'franchisee', $column);
                    try {
                        $table->renameColumn($column, $oldColumn);
                    } catch (\Exception $e) {
                        // Column might not exist or already be renamed
                    }
                });
            }
        }

        // Rename table back
        if (Schema::hasTable('franchises')) {
            Schema::rename('franchises', 'franchisees');
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}; 