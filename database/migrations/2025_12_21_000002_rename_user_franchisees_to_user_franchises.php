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

        // Rename the pivot table: user_franchisees → user_franchises
        if (Schema::hasTable('user_franchisees') && !Schema::hasTable('user_franchises')) {
            // Drop foreign key constraints first
            Schema::table('user_franchisees', function (Blueprint $table) {
                try {
                    $table->dropForeign(['user_id']);
                    $table->dropForeign(['franchise_id']);
                } catch (\Exception $e) {
                    // Constraints might not exist
                }
            });

            // Rename the table
            Schema::rename('user_franchisees', 'user_franchises');

            // Add back foreign key constraints with updated references
            Schema::table('user_franchises', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('franchise_id')
                    ->references('franchise_id')
                    ->on('franchises')
                    ->onDelete('cascade');
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

        if (Schema::hasTable('user_franchises')) {
            // Drop foreign key constraints first
            Schema::table('user_franchises', function (Blueprint $table) {
                try {
                    $table->dropForeign(['user_id']);
                    $table->dropForeign(['franchise_id']);
                } catch (\Exception $e) {
                    // Constraints might not exist
                }
            });

            // Rename the table back
            Schema::rename('user_franchises', 'user_franchisees');

            // Add back foreign key constraints with original references
            Schema::table('user_franchisees', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('franchise_id')
                    ->references('franchise_id')
                    ->on('franchises')
                    ->onDelete('cascade');
            });
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}; 