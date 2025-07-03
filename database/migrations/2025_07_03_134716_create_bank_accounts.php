<?php
// create_bank_accounts_table.php PLAID Integration

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('bank_accounts')) {
            Schema::create('bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('franchise_id');
                $table->string('access_token');
                $table->string('item_id');
                $table->timestamps();
            });
        }
        // Add foreign key constraint
        DB::statement('ALTER TABLE bank_accounts ADD CONSTRAINT fk_franchise_id FOREIGN KEY (franchise_id) REFERENCES franchises(id) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
