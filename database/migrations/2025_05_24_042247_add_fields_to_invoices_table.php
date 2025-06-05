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
        if (Schema::hasTable('invoices') && ! Schema::hasColumn('invoices', 'payment_status')) {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('updated_at');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('due_date');
            $table->text('notes_internal')->nullable()->after('payment_status');
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
             $table->dropColumn(['due_date', 'payment_status', 'notes_internal']);
        });
        }
    }
};
