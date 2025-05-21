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
            Schema::create('fgp_category_fgp_item', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_ID');
                $table->unsignedBigInteger('fgp_item_id');
                $table->timestamps();

                // Foreign keys
                $table->foreign('category_ID')->references('category_ID')->on('fgp_categories')->onDelete('cascade');
                $table->foreign('fgp_item_id')->references('fgp_item_id')->on('fgp_items')->onDelete('cascade');
            });
        }

        public function down()
        {
            Schema::dropIfExists('fgp_category_fgp_item');
        }

    };
