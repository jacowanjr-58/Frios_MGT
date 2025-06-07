<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // If you want a flexible string:
            $table->string('type', 50)->change();

            // Or, if you’d rather keep ENUM but add new values:
            // $table->enum('type', [
            //     'add',
            //     'subtract',
            //     'received_from_order',
            //     'order_add',      // newly added
            //     'manual_adjust',  // etc…
            // ])->default('add')->change();
        });
    }

    public function down()
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // revert back to your original definition
            $table->enum('type', ['add','subtract','received_from_order'])->default('add')->change();
            // —or—
            // $table->string('type', 10)->change();
        });
    }
};
