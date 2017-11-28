<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSumulaJogadorGolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumula_jogador_gols', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sumula_jogador_id');
            $table->string('tempo',5);
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
        Schema::drop('sumula_jogador_gols');
    }
}
