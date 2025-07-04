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
        if (! Schema::hasTable('fgp_orders')) {
        Schema::create('fgp_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->nullable()->constrained('franchises', 'id');
            $table->string('order_num')->unique();
            $table->json('ACH_data')->nullable();
            $table->string('ship_to_name')->nullable();
            $table->string('ship_to_address1')->nullable();
            $table->string('ship_to_address2')->nullable();
            $table->string('ship_to_city', 100)->nullable();
            $table->string('ship_to_state', 100)->nullable();
            $table->string('ship_to_zip', 20)->nullable();
            $table->string('ship_to_country', 100)->nullable();
            $table->string('ship_to_phone', 50)->nullable();
            $table->string('ship_method', 100)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('additional_charges', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->boolean('is_paid')->default(0);
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
            $table->softDeletes();
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fgp_orders');
    }
};
