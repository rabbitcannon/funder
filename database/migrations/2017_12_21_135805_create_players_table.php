<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('registrar_id',32)->index();
            $table->string('playerhash')->default('');
            $table->integer('activateddatetime')->default(0);
            $table->integer('lastlogindatetime')->default(0);
            $table->integer('cashbalancepence')->default(0);
            $table->string('username',32)->default('');
            $table->string('firstname',64)->default('');
            $table->string('lastname',64)->default('');
            $table->string('phone',32)->default('');
            $table->string('email',128)->default('');
            $table->string('address1',64)->default('');
            $table->string('address2',64)->default('');
            $table->string('city',32)->default('');
            $table->string('state',32)->default('');
            $table->string('zip',32)->default('');
            $table->integer('playerstate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('players');
    }
}
