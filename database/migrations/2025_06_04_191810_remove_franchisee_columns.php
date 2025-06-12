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
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key if it exists
            if (Schema::hasColumn('users', 'franchisee_id')) {
                $table->dropForeign(['franchisee_id']);
                $table->dropColumn('franchisee_id');
            }
        });

        Schema::table('franchisees', function (Blueprint $table) {
            // Drop foreign key if it exists
            if (Schema::hasColumn('franchisees', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('franchisee_id')->nullable();
            $table->foreign('franchisee_id')->references('id')->on('franchisees')->onDelete('set null');
        });

        Schema::table('franchisees', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

};
