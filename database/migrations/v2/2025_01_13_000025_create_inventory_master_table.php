<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create table if not exists
        if (! Schema::hasTable('inventory_master')) {
            Schema::create('inventory_master', function (Blueprint $table) {
                $table->id(); 
                $table->foreignId('franchise_id')->constrained();
                $table->foreignId('fgp_item_id')->nullable()->constrained();
                $table->string('custom_item_name')->nullable();
                $table->decimal('default_cost', 10, 2)->nullable();
                $table->integer('total_quantity')->default(0);
                $table->unsignedInteger('split_factor')->default(1)->comment('Number of units per case');
                $table->decimal('cogs_case', 10, 2)->nullable()->comment('COGS per case');
                $table->decimal('cogs_unit', 10, 2)->nullable()->comment('COGS per single unit');
                $table->decimal('wholesale_case', 10, 2)->nullable()->comment('Wholesale price per case');
                $table->decimal('wholesale_unit', 10, 2)->nullable()->comment('Wholesale price per unit');
                $table->decimal('retail_case', 10, 2)->nullable()->comment('Retail price per case');
                $table->decimal('retail_unit', 10, 2)->nullable()->comment('Retail price per unit');
                $table->timestamps();

            });
        } else {
            // Add columns if table already exists
            Schema::table('inventory_master', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_master', 'split_factor')) {
                    $table->unsignedInteger('split_factor')->default(1)->after('total_quantity')->comment('Number of units per case');
                }
                if (!Schema::hasColumn('inventory_master', 'cogs_case')) {
                    $table->decimal('cogs_case', 10, 2)->nullable()->after('split_factor')->comment('COGS per case');
                }
                if (!Schema::hasColumn('inventory_master', 'cogs_unit')) {
                    $table->decimal('cogs_unit', 10, 2)->nullable()->after('cogs_case')->comment('COGS per single unit');
                }
                if (!Schema::hasColumn('inventory_master', 'wholesale_case')) {
                    $table->decimal('wholesale_case', 10, 2)->nullable()->after('cogs_unit')->comment('Wholesale price per case');
                }
                if (!Schema::hasColumn('inventory_master', 'wholesale_unit')) {
                    $table->decimal('wholesale_unit', 10, 2)->nullable()->after('wholesale_case')->comment('Wholesale price per unit');
                }
                if (!Schema::hasColumn('inventory_master', 'retail_case')) {
                    $table->decimal('retail_case', 10, 2)->nullable()->after('wholesale_unit')->comment('Retail price per case');
                }
                if (!Schema::hasColumn('inventory_master', 'retail_unit')) {
                    $table->decimal('retail_unit', 10, 2)->nullable()->after('retail_case')->comment('Retail price per unit');
                }
            });
        }

        // Add new fields to inventory_allocations table
        Schema::table('inventory_allocations', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_allocations', 'allocated_cases')) {
                $table->unsignedInteger('allocated_cases')->default(0)->after('location_id')->comment('Full cases allocated');
            }
            if (!Schema::hasColumn('inventory_allocations', 'allocated_units')) {
                $table->unsignedInteger('allocated_units')->default(0)->after('allocated_cases')->comment('Loose units allocated');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_master');

        // Optionally remove added columns from inventory_allocations (if needed)
        Schema::table('inventory_allocations', function (Blueprint $table) {
            $table->dropColumn(['allocated_cases', 'allocated_units']);
        });
    }
};
