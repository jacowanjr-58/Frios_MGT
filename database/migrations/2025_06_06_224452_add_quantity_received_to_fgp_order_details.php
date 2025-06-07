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
        Schema::table('fgp_order_details', function (Blueprint $table) {
            // // after() is optional—pick a column to slot it in next to
            $table->unsignedInteger('quantity_received')->default(0)->after('unit_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fgp_order_details', function (Blueprint $table) {
            $table->dropColumn('quantity_received');
        });
    }
};
