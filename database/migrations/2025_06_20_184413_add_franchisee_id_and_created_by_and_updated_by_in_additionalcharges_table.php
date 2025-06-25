<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('additionalcharges') && !Schema::hasColumn('additionalcharges', 'franchise_id')) {
            Schema::table('additionalcharges', function (Blueprint $table) {
                $table->unsignedBigInteger('franchise_id')->nullable();
                $table->foreign('franchise_id')->references('user_id')->on('users')->onDelete('set null');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('additionalcharges') && Schema::hasColumn('additionalcharges', 'franchise_id')) {
            Schema::table('additionalcharges', function (Blueprint $table) {
                $table->dropForeign(['franchise_id']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropColumn(['franchise_id', 'created_by', 'updated_by']);
            });
        }
    }
};
