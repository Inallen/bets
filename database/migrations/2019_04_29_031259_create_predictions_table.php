<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePredictionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('start_time');
            $table->decimal('handicap', 6, 1);
            $table->decimal('score', 6, 1);
            $table->unsignedTinyInteger('scene')->default(0);
            $table->unsignedTinyInteger('prediction_type')->default(0);
            $table->unsignedTinyInteger('prediction_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('predictions');
    }
}
