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

           // 1️⃣ Add split_factor to inventory_master so you know how many units per case
        Schema::table('inventory_master', function (Blueprint $table) {
            $table->unsignedInteger('split_factor')
                  ->default(1)
                  ->after('total_quantity')
                  ->comment('Number of units per case');

              $table->decimal('cogs_case',      10, 2)
                  ->nullable()
                  ->after('split_factor')
                  ->comment('COGS per case');

            $table->decimal('cogs_unit',      10, 2)
                  ->nullable()
                  ->after('cogs_case')
                  ->comment('COGS per single unit');

            $table->decimal('wholesale_case', 10, 2)
                  ->nullable()
                  ->after('cogs_unit')
                  ->comment('Wholesale price per case');

            $table->decimal('wholesale_unit', 10, 2)
                  ->nullable()
                  ->after('wholesale_case')
                  ->comment('Wholesale price per unit');

            $table->decimal('retail_case',    10, 2)
                  ->nullable()
                  ->after('wholesale_unit')
                  ->comment('Retail price per case');

            $table->decimal('retail_unit',    10, 2)
                  ->nullable()
                  ->after('retail_case')
                  ->comment('Retail price per unit');
        });

        // 2️⃣ Add separate case & unit fields to allocations
        Schema::table('inventory_allocations', function (Blueprint $table) {
            $table->unsignedInteger('allocated_cases')
                  ->default(0)
                   ->after('location_id')
                  ->comment('Full cases allocated');
            $table->unsignedInteger('allocated_units')
                  ->default(0)
                  ->after('allocated_cases')
                  ->comment('Loose units allocated');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

       Schema::table('inventory_allocations', function (Blueprint $table) {
            $table->dropColumn(['allocated_cases', 'allocated_units']);
        });

        Schema::table('inventory_master', function (Blueprint $table) {
            $table->dropColumn([
                'split_factor',
                'cogs_case',
                'cogs_unit',
                'wholesale_case',
                'wholesale_unit',
                'retail_case',
                'retail_unit',
            ]);
        });
    }
};
