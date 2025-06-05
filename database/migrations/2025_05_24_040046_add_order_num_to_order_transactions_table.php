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
        if (Schema::hasTable('order_transactions') && ! Schema::hasColumn('order_transactions', 'order_num')) {
        Schema::table('order_transactions', function (Blueprint $table) {
             $table->string('order_num')->after('fgp_order_id')->nullable()->unique();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('order_transactions')) {
        Schema::table('order_transactions', function (Blueprint $table) {
             $table->dropColumn('order_num');
        });
        }
    }
};
