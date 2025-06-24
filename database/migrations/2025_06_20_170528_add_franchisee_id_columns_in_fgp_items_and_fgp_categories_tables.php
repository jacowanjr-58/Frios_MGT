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
        Schema::table('fgp_items', function (Blueprint $table) {
            $table->unsignedBigInteger('franchise_id')->nullable();
            $table->foreign('franchise_id')
                ->references('franchise_id')
                ->on('franchises')
                ->onDelete('set null');
        });
        Schema::table('fgp_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('franchise_id')->nullable();
            $table->foreign('franchise_id')
                ->references('franchise_id')
                ->on('franchises')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fgp_items', function (Blueprint $table) {
            $table->dropForeign(['franchise_id']);
            $table->dropColumn(['franchise_id']);
        });
        Schema::table('fgp_categories', function (Blueprint $table) {
            $table->dropForeign(['franchise_id']);
            $table->dropColumn(['franchise_id']);
        });
    }
};
