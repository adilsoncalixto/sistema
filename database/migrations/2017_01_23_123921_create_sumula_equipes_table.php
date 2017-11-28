<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSumulaEquipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumula_equipes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sumula_id');
            $table->integer('equipe_lado');
            $table->integer('equipe_id');
            $table->timestamps();
            $table->index(['equipe_lado','equipe_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sumula_equipes');
    }
}
