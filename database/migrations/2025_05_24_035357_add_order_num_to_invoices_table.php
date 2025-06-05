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
        if (Schema::hasTable('invoices') && ! Schema::hasColumn('invoices', 'order_num')) {
        Schema::table('invoices', function (Blueprint $table) {
             $table->string('order_num')->after('franchisee_id')->nullable()->unique();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
        Schema::table('invoices', function (Blueprint $table) {
              $table->dropColumn('order_num');
        });
        }
    }
};
