<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirigenteDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dirigente_documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dirigente_id')->unsigned()->index();
            $table->foreign('dirigente_id')->references('id')->on('dirigentes');
            $table->string('documento_cpf');
            $table->string('documento_rg');
            $table->string('documento_cr');
            $table->string('foto_dirigente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dirigente_documentos');
    }
}
