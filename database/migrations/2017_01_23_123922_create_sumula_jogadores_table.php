<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSumulaJogadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumula_jogadores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sumula_id');
            $table->integer('equipe_id');
            $table->integer('jogador_id');
            $table->integer('inicia')->default(0);
            $table->integer('camisa');
            $table->integer('gols');
            $table->integer('faltas');
            $table->string('amarelo',5);
            $table->string('vermelho',5);
            $table->timestamps();
            $table->index(['sumula_id','equipe_id','jogador_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sumula_jogadores');
    }
}
