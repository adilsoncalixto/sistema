<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|


Route::get('/', function () {
    return view('welcome');
});
*/


Route::Get('/', 'HomeController@index');

Route::match(['get','post'],'/auth/login', 'LoginController@login');
Route::match(['get','post'],'/auth/logout', 'LoginController@logout');

Route::group(['middleware' => 'auth'], function () {


    Route::get('/dashboard', 'DashBoardController@index');

    Route::get('/usuario/lista', 'UsuarioController@index');
    Route::post('/usuario/meusdados', 'UsuarioController@meusDados');
    Route::post('/usuario/trocasenha', 'UsuarioController@trocaSenha');
    Route::match(['get','post'],'/usuario/cadastro/{idUsuario?}', 'UsuarioController@cadastro');
    Route::post('/usuario/exclui', 'UsuarioController@exclui');
    Route::post('/usuario/reset', 'UsuarioController@reset');

    Route::get('/categoria/lista', 'CategoriasController@index');
    Route::match(['get','post'],'/categoria/cadastro/{idUsuario?}', 'CategoriasController@cadastro');
    Route::post('/categoria/exclui', 'CategoriasController@exclui');

    Route::get('/equipe/lista', 'EquipeController@index');
    Route::match(['get','post'],'/equipe/cadastro/{idUsuario?}', 'EquipeController@cadastro');
    Route::post('/equipe/exclui', 'EquipeController@exclui');

    Route::get('/dirigente/lista', 'DirigenteController@index');
    Route::match(['get','post'],'/dirigente/cadastro/{idUsuario?}', 'DirigenteController@cadastro');
    Route::post('/dirigente/exclui', 'DirigenteController@exclui');

    Route::post('/dirigente/grava/documento/foto', 'DirigenteController@gravaDocumentoFoto');
    Route::post('/dirigente/grava/documento/cpf', 'DirigenteController@gravaDocumentoCPF');
    Route::post('/dirigente/grava/documento/rg', 'DirigenteController@gravaDocumentoRG');
    Route::post('/dirigente/grava/documento/cr', 'DirigenteController@gravaDocumentoCR');

    Route::get('/dirigente/remove/documento/foto/{iddirigente}', 'DirigenteController@removeDocumentoFoto');
    Route::get('/dirigente/remove/documento/cpf/{iddirigente}', 'DirigenteController@removeDocumentoCpf');
    Route::get('/dirigente/remove/documento/rg/{iddirigente}', 'DirigenteController@removeDocumentoRg');
    Route::get('/dirigente/remove/documento/cr/{iddirigente}', 'DirigenteController@removeDocumentoCr');

    Route::get('/dirigente/carteirinha/{id}', 'DirigenteController@carteirinha');


    Route::get('/dirigente/equipes/{dirigente?}', 'DirigenteEquipesController@index');
    Route::get('/dirigente/equipes/edit/{id}', 'DirigenteEquipesController@show');
    Route::get('/dirigente/equipes/exclui/{id}', 'DirigenteEquipesController@destroy');
    Route::post('/dirigente/equipe/gravar', 'DirigenteEquipesController@store');

    Route::get('/jogador/lista', 'JogadorController@index');
    Route::get('/jogador/carteirinha/{id}', 'JogadorController@carteirinha');
    Route::match(['get','post'],'/jogador/cadastro/{idUsuario?}', 'JogadorController@cadastro');
    Route::post('/jogador/exclui', 'JogadorController@exclui');

    Route::post('/jogador/grava/documento/foto', 'JogadorController@gravaDocumentoFoto');
    Route::post('/jogador/grava/documento/cpf', 'JogadorController@gravaDocumentoCPF');
    Route::post('/jogador/grava/documento/rg', 'JogadorController@gravaDocumentoRG');
    Route::post('/jogador/grava/documento/cr', 'JogadorController@gravaDocumentoCR');

    Route::get('/jogador/remove/documento/foto/{idJogador}', 'JogadorController@removeDocumentoFoto');
    Route::get('/jogador/remove/documento/cpf/{idJogador}', 'JogadorController@removeDocumentoCpf');
    Route::get('/jogador/remove/documento/rg/{idJogador}', 'JogadorController@removeDocumentoRg');
    Route::get('/jogador/remove/documento/cr/{idJogador}', 'JogadorController@removeDocumentoCr');


    Route::group(['prefix' => 'tabelas', 'as' => 'tabelas.', 'namespace' => 'Tabelas'], function() {

        // local

        Route::group(['prefix' => 'locais', 'as' => 'locais.'], function() {
            Route::get('', ['as' => 'index', 'uses' => 'LocalController@index']);
            Route::get('novo', ['as' => 'create', 'uses' => 'LocalController@create']);
            Route::post('store', ['as' => 'store', 'uses' => 'LocalController@store']);
            Route::get('editar/{id}', ['as' => 'edit', 'uses' => 'LocalController@show']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'LocalController@update']);
            Route::post('destroy', ['as' => 'destroy', 'uses' => 'LocalController@destroy']);
            Route::get('destroy/{id}', ['as' => 'destroy', 'uses' => 'LocalController@destroy']);
        });

        Route::group(['prefix' => 'criterios', 'as' => 'criterios.'], function() {
            Route::get('', ['as' => 'index', 'uses' => 'CriterioController@index']);
            Route::get('novo', ['as' => 'create', 'uses' => 'CriterioController@create']);
            Route::post('store', ['as' => 'store', 'uses' => 'CriterioController@store']);
            Route::get('editar/{id}', ['as' => 'edit', 'uses' => 'CriterioController@show']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'CriterioController@update']);
            Route::post('destroy', ['as' => 'destroy', 'uses' => 'CriterioController@destroy']);
            Route::get('destroy/{id}', ['as' => 'destroy', 'uses' => 'CriterioController@destroy']);
        });

        Route::group(['prefix' => 'arbitros', 'as' => 'arbitros.'], function() {
            Route::get('', ['as' => 'index', 'uses' => 'ArbitroController@index']);
            Route::get('novo', ['as' => 'create', 'uses' => 'ArbitroController@create']);
            Route::post('store', ['as' => 'store', 'uses' => 'ArbitroController@store']);
            Route::get('editar/{id}', ['as' => 'edit', 'uses' => 'ArbitroController@show']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'ArbitroController@update']);
            Route::post('destroy', ['as' => 'destroy', 'uses' => 'ArbitroController@destroy']);
            Route::get('destroy/{id}', ['as' => 'destroy', 'uses' => 'ArbitroController@destroy']);
        });

        Route::group(['prefix' => 'competicoes', 'as' => 'competicoes.'], function() {
            Route::get('', ['as' => 'index', 'uses' => 'CompeticaoController@index']);
            Route::get('novo', ['as' => 'create', 'uses' => 'CompeticaoController@create']);
            Route::post('store', ['as' => 'store', 'uses' => 'CompeticaoController@store']);
            Route::get('editar/{id}', ['as' => 'edit', 'uses' => 'CompeticaoController@show']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'CompeticaoController@update']);
            Route::post('destroy', ['as' => 'destroy', 'uses' => 'CompeticaoController@destroy']);
            Route::get('destroy/{id}', ['as' => 'destroy', 'uses' => 'CompeticaoController@destroy']);
        });

        Route::group(['prefix' => 'partidas', 'as' => 'partidas.'], function() {
            Route::get('', ['as' => 'index', 'uses' => 'PartidaController@index']);
            Route::get('novo', ['as' => 'create', 'uses' => 'PartidaController@create']);
            Route::post('store', ['as' => 'store', 'uses' => 'PartidaController@store']);
            Route::get('editar/{id}', ['as' => 'edit', 'uses' => 'PartidaController@show']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'PartidaController@update']);
            Route::post('destroy', ['as' => 'destroy', 'uses' => 'PartidaController@destroy']);
            Route::get('destroy/{id}', ['as' => 'destroy', 'uses' => 'PartidaController@destroy']);
        });

        Route::group(['prefix' => 'sumulas', 'as' => 'sumulas.'], function() {
            Route::get('', ['as' => 'index', 'uses' => 'SumulaController@index']);
            Route::get('pre', ['as' => 'pre', 'uses' => 'SumulaController@presumula']);
            Route::get('nova', ['as' => 'create', 'uses' => 'SumulaController@create']);
            Route::post('store', ['as' => 'store', 'uses' => 'SumulaController@store']);
            Route::get('editar/{id}', ['as' => 'edit', 'uses' => 'SumulaController@show']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'SumulaController@update']);
            Route::post('destroy', ['as' => 'destroy', 'uses' => 'SumulaController@destroy']);
            Route::get('destroy/{id}', ['as' => 'destroy', 'uses' => 'SumulaController@destroy']);
            Route::get('pdf/{id}', ['as' => 'pdf', 'uses' => 'SumulaController@pdf']);

            Route::post('savehistorico', ['as' => 'savehistorico', 'uses' => 'SumulaController@savehistorico']);
            Route::post('deletahistorico', ['as' => 'deletahistorico', 'uses' => 'SumulaController@deletahistorico']);

        });
    });

    Route::group(['prefix' => 'relatorios', 'as' => 'relatorios.', 'namespace' => 'Relatorios'], function() {


            Route::get('equipes', ['as' => 'equipes', 'uses' => 'EquipesController@index']);
            Route::post('equipe/jogadores', ['as' => 'equipe.jogadores', 'uses' => 'EquipesController@equipeJogadores']);
            Route::get('equipe/jogadores1/{eq}/{cp}/{pt}', ['as' => 'equipe.jogadores1', 'uses' => 'EquipesController@equipeJogadoresget']);
            Route::get('artilharia', ['as' => 'artilharia', 'uses' => 'ArtilhariaController@index']);
            Route::post('artilharia/listagem', ['as' => 'artilharia.listagem', 'uses' => 'ArtilhariaController@showPdf']);
            Route::get('classificacao', ['as' => 'classificacao', 'uses' => 'ClassificacaoController@index']);
            Route::post('classificacao/listagem', ['as' => 'classificacao.listagem', 'uses' => 'ClassificacaoController@showPdf']);
   ;

    });
    

    Route::group(['prefix' => 'ajax', 'as' => 'ajax.', 'namespace' => 'Ajax'], function() {

        Route::group(['prefix' => 'sumulas', 'as' => 'sumulas.'], function() {
            Route::any('partidas', ['as' => 'partidas', 'uses' => 'SumulaController@partidas']);
            Route::any('locais', ['as' => 'locais', 'uses' => 'SumulaController@locais']);
            Route::any('partida', ['as' => 'partida', 'uses' => 'SumulaController@equipes']);
            Route::any('equipe/jogadores', ['as' => 'equipes.jogadores', 'uses' => 'SumulaController@jogadoresDaEquipe']);
            Route::any('equipe/dirigentes', ['as' => 'equipes.dirigentes', 'uses' => 'SumulaController@dirigentesDaEquipe']);
            Route::any('pre/gravar', ['as' => 'pre.gravar', 'uses' => 'SumulaController@gravaPreSumula']);
        });

    });



});

