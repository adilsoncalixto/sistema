<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSumulasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumulas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('competicao_id');
            $table->integer('partida_id');
            $table->date('data_partida');
            $table->string('marcador',50)->nullable();
            $table->string('cronometrista',50)->nullable();
            $table->string('periodo_1_inicio',5)->nullable();
            $table->string('periodo_1_termino',5)->nullable();
            $table->string('periodo_2_inicio',5)->nullable();
            $table->string('periodo_2_termino',5)->nullable();
            $table->string('periodo_3_inicio',5)->nullable();
            $table->string('periodo_3_termino',5)->nullable();
            $table->string('periodo_4_inicio',5)->nullable();
            $table->string('periodo_4_termino',5)->nullable();


            $table->timestamps();
            $table->index(['competicao_id','partida_id'],'sumula_ukey');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sumulas');
    }
}
