<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('left_team_id');
            $table->unsignedBigInteger('right_team_id');
            $table->unsignedInteger('left_team_score')->default(0);
            $table->unsignedInteger('right_team_score')->default(0);
            $table->string('result')->nullable();
            $table->unsignedBigInteger('start_time')->default(0);
            $table->unsignedTinyInteger('match_type')->default(0);
            $table->unsignedTinyInteger('match_status')->default(0);
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
        Schema::dropIfExists('matches');
    }
}
