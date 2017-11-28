<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('competicao_id');
            $table->timestamp('inicio');
            $table->timestamp('termino');
            $table->integer('local_id');
            $table->integer('equipe1_id');
            $table->integer('equipe2_id');
            $table->integer('arbitro1_id');
            $table->integer('arbitro2_id');
            $table->integer('arbitro3_id');
            $table->string('historico');
            $table->string('vencedor');
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
        Schema::drop('partidas');
    }
}
