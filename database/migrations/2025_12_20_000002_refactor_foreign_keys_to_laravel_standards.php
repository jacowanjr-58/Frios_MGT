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

        // 1. Fix fgp_orders foreign key relationships
        // Note: user_ID -> user_id rename is now handled in the first migration
        
        // Fix user_id column type and add foreign key
        if (Schema::hasColumn('fgp_orders', 'user_id')) {
            // First, change the column type to match users.id (bigint unsigned)
            Schema::table('fgp_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->change();
            });
            
            // Then add the foreign key constraint (if it doesn't already exist)
            try {
                Schema::table('fgp_orders', function (Blueprint $table) {
                    $table->foreign('user_id')
                        ->references('user_id')
                        ->on('users')
                        ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key already exists, skip
            }
        }
        
        // Fix customer_id column type and add foreign key
        if (Schema::hasColumn('fgp_orders', 'customer_id')) {
            // First, change the column type to match customers.id (bigint unsigned)
            Schema::table('fgp_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_id')->change();
            });
            
            // Then add the foreign key constraint (if it doesn't already exist)
            try {
                Schema::table('fgp_orders', function (Blueprint $table) {
                    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key already exists, skip
            }
        }

        // 2. Fix foreign keys in fgp_items table  
        if (Schema::hasColumn('fgp_items', 'category_ID')) {
            Schema::table('fgp_items', function (Blueprint $table) {
                // Drop existing foreign key (we know it exists from inspection)
                $table->dropForeign('fgp_items_category_id_foreign');
                // Rename column from category_ID to category_id
                $table->renameColumn('category_ID', 'category_id');
            });
            
            // Add proper foreign key constraint
            try {
                Schema::table('fgp_items', function (Blueprint $table) {
                    $table->foreign('category_id')->references('id')->on('fgp_categories')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key already exists, skip
            }
        }

        // 3. Update fgp_order_details foreign keys (if the table exists)
        if (Schema::hasTable('fgp_order_details')) {
            // Check if we need to rename fgp_ordersID to fgp_order_id
            if (Schema::hasColumn('fgp_order_details', 'fgp_ordersID')) {
                Schema::table('fgp_order_details', function (Blueprint $table) {
                    // Try to drop foreign key constraint with different possible names
                    try {
                        $table->dropForeign('fgp_order_details_fgp_order_id_foreign');
                    } catch (\Exception $e) {
                        try {
                            $table->dropForeign(['fgp_ordersID']);
                        } catch (\Exception $e2) {
                            try {
                                $table->dropForeign(['fgp_order_id']);
                            } catch (\Exception $e3) {
                                // No foreign key exists
                            }
                        }
                    }
                    
                    // Rename the column
                    $table->renameColumn('fgp_ordersID', 'fgp_order_id');
                });
            }
            
            // Add proper foreign key constraints (checking that columns exist)
            if (Schema::hasColumn('fgp_order_details', 'fgp_order_id')) {
                try {
                    Schema::table('fgp_order_details', function (Blueprint $table) {
                        $table->foreign('fgp_order_id')->references('id')->on('fgp_orders')->onDelete('cascade');
                    });
                } catch (\Exception $e) {
                    // Foreign key already exists, skip
                }
            }
            
            if (Schema::hasColumn('fgp_order_details', 'fgp_item_id')) {
                try {
                    Schema::table('fgp_order_details', function (Blueprint $table) {
                        $table->foreign('fgp_item_id')->references('id')->on('fgp_items')->onDelete('cascade');
                    });
                } catch (\Exception $e) {
                    // Foreign key already exists, skip
                }
            }
        }

        // 4. Update customers table foreign key reference
        if (Schema::hasColumn('customers', 'franchise_id')) {
            Schema::table('customers', function (Blueprint $table) {
                // Drop existing foreign key
                try {
                    $table->dropForeign(['franchise_id']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
            });
            
            // Add proper foreign key constraint - check if franchises table exists
            // If not, we'll let a later migration handle this
            if (Schema::hasTable('franchises') && Schema::hasColumn('franchises', 'franchise_id')) {
                Schema::table('customers', function (Blueprint $table) {
                    $table->foreign('franchise_id')
                        ->references('franchise_id')
                        ->on('franchises')
                        ->onDelete('cascade');
                });
            } elseif (Schema::hasTable('franchisees') && Schema::hasColumn('franchisees', 'franchise_id')) {
                // If franchises table doesn't exist yet but franchisees does
                Schema::table('customers', function (Blueprint $table) {
                    $table->foreign('franchise_id')
                        ->references('franchise_id')
                        ->on('franchisees')
                        ->onDelete('cascade');
                });
            }
            // If neither table exists or has the right column, skip this step
            // A later migration will handle the foreign key constraint
        }

        // 5. Update sessions table foreign key
        // if (Schema::hasColumn('sessions', 'user_id')) {
        //     Schema::table('sessions', function (Blueprint $table) {
        //         // Drop existing foreign key if it exists
        //         try {
        //             $table->dropIndex(['user_id']);
        //         } catch (\Exception $e) {
        //             // Index might not exist
        //         }
        //     });
            
        //     // Add proper foreign key constraint
        //     Schema::table('sessions', function (Blueprint $table) {
        //         $table->foreign('user_id')
        //             ->references('user_id')
        //             ->on('users')
        //             ->onDelete('cascade');
        //     });
        // }

        // 6. Update pivot table fgp_category_fgp_item
        // if (Schema::hasTable('fgp_category_fgp_item')) {
        //     // Check if the columns exist before trying to manipulate them
        //     if (Schema::hasColumn('fgp_category_fgp_item', 'category_ID')) {
        //         Schema::table('fgp_category_fgp_item', function (Blueprint $table) {
        //             // Drop existing foreign keys if they exist - try different possible constraint names
        //             $possibleCategoryConstraints = [
        //                 'fgp_category_fgp_item_category_id_foreign',
        //                 'fgp_category_fgp_item_category_ID_foreign',
        //                 'category_ID',
        //                 'category_id'
        //             ];
                    
        //             foreach ($possibleCategoryConstraints as $constraint) {
        //                 try {
        //                     if (is_numeric(array_search($constraint, ['category_ID', 'category_id']))) {
        //                         $table->dropForeign([$constraint]);
        //                     } else {
        //                         $table->dropForeign($constraint);
        //                     }
        //                     break; // If successful, stop trying other names
        //                 } catch (\Exception $e) {
        //                     // Constraint doesn't exist with this name, try next
        //                 }
        //             }
                    
        //             $possibleItemConstraints = [
        //                 'fgp_category_fgp_item_fgp_item_id_foreign',
        //                 'fgp_item_id'
        //             ];
                    
        //             foreach ($possibleItemConstraints as $constraint) {
        //                 try {
        //                     if ($constraint === 'fgp_item_id') {
        //                         $table->dropForeign([$constraint]);
        //                     } else {
        //                         $table->dropForeign($constraint);
        //                     }
        //                     break; // If successful, stop trying other names
        //                 } catch (\Exception $e) {
        //                     // Constraint doesn't exist with this name, try next
        //                 }
        //             }
                    
        //             // Rename the column
        //             $table->renameColumn('category_ID', 'category_id');
        //         });
                
        //         // Add proper foreign key constraints
        //         try {
        //             Schema::table('fgp_category_fgp_item', function (Blueprint $table) {
        //                 $table->foreign('category_id')->references('id')->on('fgp_categories')->onDelete('cascade');
        //                 $table->foreign('fgp_item_id')->references('id')->on('fgp_items')->onDelete('cascade');
        //             });
        //         } catch (\Exception $e) {
        //             // Foreign keys might already exist
        //         }
        //     }
        // }

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

        // Reverse all changes
        
        // 1. Reverse fgp_orders
        // if (Schema::hasColumn('fgp_orders', 'user_id')) {
        //     Schema::table('fgp_orders', function (Blueprint $table) {
        //         $table->dropForeign(['user_id']);
        //         $table->renameColumn('user_id', 'user_ID');
        //     });
        // }

        // 2. Reverse fgp_items
        if (Schema::hasColumn('fgp_items', 'category_id')) {
            Schema::table('fgp_items', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->renameColumn('category_id', 'category_ID');
            });
            
            Schema::table('fgp_items', function (Blueprint $table) {
                $table->foreign('category_ID')->references('category_ID')->on('fgp_categories')->onDelete('cascade');
            });
        }

        // 3. Reverse pivot table
        if (Schema::hasTable('fgp_category_fgp_item') && Schema::hasColumn('fgp_category_fgp_item', 'category_id')) {
            Schema::table('fgp_category_fgp_item', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropForeign(['fgp_item_id']);
                $table->renameColumn('category_id', 'category_ID');
            });
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}; 