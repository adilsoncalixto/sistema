<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricoPartidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('historico_partidas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sumula')->unsigned()->index();
            $table->integer('id_jogador')->unsigned()->index();
            $table->integer('id_equipe')->unsigned()->index();
            $table->integer('id_competicao')->unsigned()->index();
            $table->integer('id_categoria')->unsigned()->index();
            $table->integer('id_sub')->unsigned()->index();
            $table->integer('camisa');
            $table->string('type_param');
            $table->string('type_form');
            $table->string('tempo');

            $table->timestamps();
        });

         Schema::table('historico_partidas', function (Blueprint $table) {
        
            $table->foreign('id_jogador')->references('id')->on('jogadores');
            $table->foreign('id_equipe')->references('id')->on('equipes');
            $table->foreign('id_competicao')->references('id')->on('competicoes');
            $table->foreign('id_categoria')->references('id')->on('categorias');
            $table->foreign('id_sub')->references('id')->on('jogadores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('historico_partidas');
    }
}
