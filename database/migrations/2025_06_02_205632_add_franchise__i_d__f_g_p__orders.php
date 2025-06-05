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
          if (Schema::hasTable('fgp_orders') && ! Schema::hasColumn('fgp_orders', 'franchisee_id')) {
          Schema::table('fgp_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('franchisee_id')->nullable()->after('user_ID');
            $table->foreign('franchisee_id')
                ->references('franchisee_id')
                ->on('franchisees')
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
        $table->dropForeign(['franchisee_id']);
        $table->dropColumn('franchisee_id');
    });
        }
    }
};
