<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fgp_items', function (Blueprint $table) {
            $table->unsignedInteger('split_factor')->default(48)->after('internal_inventory'); // 48 pops per case as default
        });
    }

    public function down(): void
    {
        Schema::table('fgp_items', function (Blueprint $table) {
            $table->dropColumn('split_factor');
        });
    }
};
