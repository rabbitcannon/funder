<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_packs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('quantum_start')->useCurrent();
            $table->timestamp('quantum_end')->useCurrent();
            $table->mediumText('pack');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_packs');
    }
}
