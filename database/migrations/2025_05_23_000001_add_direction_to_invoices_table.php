<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoices') && ! Schema::hasColumn('invoices', 'direction')) {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('direction', ['payable', 'receivable'])->default('receivable')->after('customer_id');
        });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('direction');
        });
        }
    }
};
