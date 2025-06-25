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
            $table->string('ein_ssn_hash')->nullable()->after('security');
            $table->string('contract_document_path')->nullable()->after('ein_ssn_hash');
            $table->date('date_joined')->nullable()->after('contract_document_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ein_ssn_hash', 'contract_document_path', 'date_joined']);
        });
    }
};
