<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirigentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dirigentes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('equipe_id')->references('id')->on('equipes');
            $table->string('nome_dirigente');
            $table->integer('dirigente_tipo_id')->references('id')->on('ditrigente_tipos');
            $table->string('cargo');
            $table->string('cpf_dirigente');
            $table->string('rg_dirigente');
            $table->string('telefone_dirigente');
            $table->string('celular_dirigente');
            $table->string('email_dirigente');
            $table->string('endereco_dirigente');
            $table->date('data_nascimento')->nullable();
            $table->date('data_validade')->nullable();
            $table->boolean('analizando')->default(false);
            $table->boolean('liberado')->default(false);
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
        Schema::drop('dirigentes');
    }
}
