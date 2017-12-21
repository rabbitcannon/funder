<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGumdropPlayerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gumdrop_player', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('player_id')->unsigned();
            $table->integer('gumdrop_id')->unsigned();

            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('gumdrop_id')->references('id')->on('gumdrops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gumdrop_player');
    }
}
