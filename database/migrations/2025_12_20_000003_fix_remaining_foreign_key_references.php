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
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Fix ups_shipments table foreign key reference
        if (Schema::hasTable('ups_shipments') && Schema::hasColumn('ups_shipments', 'fgp_ordersID')) {
            Schema::table('ups_shipments', function (Blueprint $table) {
                // Drop existing foreign key
                try {
                    $table->dropForeign(['fgp_ordersID']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
                // Rename column
                $table->renameColumn('fgp_ordersID', 'fgp_order_id');
            });
            
            // Add proper foreign key constraint
            Schema::table('ups_shipments', function (Blueprint $table) {
                $table->foreign('fgp_order_id')->references('id')->on('fgp_orders')->onDelete('cascade');
            });
        }

        // Fix order_discrepancies table foreign key reference
        if (Schema::hasTable('order_discrepancies')) {
            Schema::table('order_discrepancies', function (Blueprint $table) {
                // Drop existing foreign key if it exists
                try {
                    $table->dropForeign(['order_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
            });
            
            // Add proper foreign key constraint
            Schema::table('order_discrepancies', function (Blueprint $table) {
                $table->foreign('order_id')->references('id')->on('fgp_orders')->onDelete('cascade');
            });
        }

        // Fix payments table foreign key reference (if it exists)
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                // Drop existing foreign key if it exists
                try {
                    $table->dropForeign(['order_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
            });
            
            // Add proper foreign key constraint
            Schema::table('payments', function (Blueprint $table) {
                $table->foreign('order_id')->references('id')->on('fgp_orders')->onDelete('cascade');
            });
        }

        // Fix any remaining foreign key references in user_franchises table
        if (Schema::hasTable('user_franchises')) {
            Schema::table('user_franchises', function (Blueprint $table) {
                try {
                    $table->dropForeign(['franchise_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
            });

            Schema::table('user_franchises', function (Blueprint $table) {
                $table->foreign('franchise_id')->references('id')->on('franchises')->onDelete('cascade');
            });
        }

        // Fix inventory_transactions table foreign keys
        if (Schema::hasTable('inventory_transactions')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                // Drop existing foreign keys if they exist
                try {
                    $table->dropForeign(['inventory_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
                try {
                    $table->dropForeign(['created_by']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
            });
            
            // Add proper foreign key constraints with updated references
            try {
                Schema::table('inventory_transactions', function (Blueprint $table) {
                    $table->foreign('inventory_id')->references('id')->on('inventory_master')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key already exists, skip
            }
            
            try {
                Schema::table('inventory_transactions', function (Blueprint $table) {
                    $table->foreign('created_by')
                        ->references('user_id')
                        ->on('users')
                        ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key already exists, skip
            }
        }

        // Fix any remaining foreign key references in events table
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                try {
                    $table->dropForeign(['franchise_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
            });

            Schema::table('events', function (Blueprint $table) {
                $table->foreign('franchise_id')->references('id')->on('franchises')->onDelete('cascade');
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

        // Reverse changes
        if (Schema::hasTable('ups_shipments') && Schema::hasColumn('ups_shipments', 'fgp_order_id')) {
            Schema::table('ups_shipments', function (Blueprint $table) {
                $table->dropForeign(['fgp_order_id']);
                $table->renameColumn('fgp_order_id', 'fgp_ordersID');
            });
            
            Schema::table('ups_shipments', function (Blueprint $table) {
                $table->foreign('fgp_ordersID')->references('fgp_ordersID')->on('fgp_orders')->onDelete('cascade');
            });
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}; 