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
        if (Schema::hasTable('fgp_orders') && ! Schema::hasColumn('fgp_orders', 'franchise_id')) {
            Schema::table('fgp_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('franchise_id')->nullable()->after('user_id');
                $table->foreign('franchise_id')
                    ->references('user_id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('fgp_orders')) {
            Schema::table('fgp_orders', function (Blueprint $table) {
                $table->dropForeign(['franchise_id']);
                $table->dropColumn('franchise_id');
            });
        }
    }
};
