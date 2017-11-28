<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJogadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jogadores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('equipe_id')->unsigned()->index();
            $table->foreign('equipe_id')->references('id')->on('equipes');
            $table->string('nome_jogador');
            $table->date('data_nascimento')->nullable();
            $table->date('data_validade')->nullable();
            $table->integer('numero_registro');
            $table->string('cpf_jogador')->unique();
            $table->string('rg_jogador');
            $table->string('telefone_jogador');
            $table->string('celular_jogador');
            $table->string('email_jogador')->nullable();
            $table->string('endereco_jogador')->nullable();
            $table->integer('analizando')->nullable();
            $table->integer('liberado')->nullable();
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
        Schema::drop('jogadores');
    }
}
