<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJogadorDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jogador_documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jogador_id')->unsigned()->index();
            $table->foreign('jogador_id')->references('id')->on('jogadores');
            $table->string('documento_cpf');
            $table->string('documento_rg');
            $table->string('documento_cr');
            $table->string('foto_jogador');
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
        Schema::drop('jogador_documentos');
    }
}
