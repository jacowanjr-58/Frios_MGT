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
        if (! Schema::hasTable('events')) {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('franchise_id');
            $table->string('event_name');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->enum('event_status' , ['scheduled' , 'tentative' , 'staffed']);
            $table->json('staff_assigned')->nullable();
            $table->integer('customer_id')->nullable();
            $table->double('expected_sales')->nullable();
            $table->double('actual_sales')->nullable();
            $table->double('costs')->nullable();
            $table->text('event_notes')->nullable();
            $table->json('resources_selection')->nullable();
            $table->string('event_type')->nullable();
            $table->enum('planned_payment' , ['cash' , 'check' , 'inovice' , 'credit-card'])->nullable();
            $table->timestamps();

            // Foreign Key
            $table->foreign('franchise_id')
                ->references('franchise_id')
                ->on('franchises')
                ->onDelete('cascade');
        });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
