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
        // Disable foreign key checks to avoid constraint issues during fixing
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Get all foreign key constraints that reference fgp_items
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND REFERENCED_TABLE_NAME = 'fgp_items'
        ");

        // Drop existing foreign key constraints
        foreach ($constraints as $constraint) {
            try {
                DB::statement("ALTER TABLE `{$constraint->TABLE_NAME}` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Constraint might not exist
            }
        }

        // Define the tables and their appropriate delete actions
        $tables = [
            'fgp_category_fgp_item' => ['column' => 'fgp_item_id', 'action' => 'CASCADE'],
            'inventories' => ['column' => 'fgp_item_id', 'action' => 'CASCADE'],
            'inventory_master' => ['column' => 'fgp_item_id', 'action' => 'SET NULL'],
            'inventory_allocations' => ['column' => 'fgp_item_id', 'action' => 'SET NULL']
        ];

        // Add correct foreign key constraints pointing to fgp_items.id
        foreach ($tables as $table => $config) {
            $column = $config['column'];
            $action = $config['action'];
            
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                try {
                    DB::statement("
                        ALTER TABLE `{$table}` 
                        ADD CONSTRAINT `{$table}_{$column}_foreign` 
                        FOREIGN KEY (`{$column}`) 
                        REFERENCES `fgp_items` (`id`) 
                        ON DELETE {$action}
                    ");
                } catch (\Exception $e) {
                    // Log the error but continue
                    echo "Warning: Could not add foreign key for {$table}.{$column}: " . $e->getMessage() . "\n";
                }
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

        // Drop the foreign key constraints we added
        $tables = [
            'fgp_category_fgp_item' => 'fgp_item_id',
            'inventories' => 'fgp_item_id',
            'inventory_master' => 'fgp_item_id',
            'inventory_allocations' => 'fgp_item_id'
        ];

        foreach ($tables as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                try {
                    DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$table}_{$column}_foreign`");
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
