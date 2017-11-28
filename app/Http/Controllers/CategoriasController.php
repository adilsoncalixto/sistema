<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Requests;

use DB;

class CategoriasController extends Controller
{
    public function index() {

        $categorias = DB::table('categorias')->get();

        return view('categorias.categorias-lista',compact('categorias'));
    }

    public function cadastro(Request $request,$idRegistro = -1) {

        if ($request->method() == 'GET') {

            $categoria = null;
            if ($idRegistro > 0) {
                $categoria = DB::table('categorias')->where('id',$idRegistro)->get();
                $categoria = $categoria[0];
            }
            //dd($categoria);
            return view('categorias.categorias-cadastro', compact('categoria'));
        }
        elseif ($request->method() == 'POST') {

            $id  = $request->input('id');

            try {
                if (strlen($id) > 0) {
                    DB::table('categorias')
                        ->where('id', $id)
                        ->update(['nome_categoria' => $request->input('nomecategoria'),
                            'adulto' => $request->input(('adulto'))]);
                }
                else {
                    DB::insert('insert into categorias (nome_categoria, adulto) values (?,?)',
                        [$request->input('nomecategoria'), $request->input('adulto')]);
                }

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A gravação do registro foi realizada com sucesso';
                $botoes = '<a href="/categoria/cadastro" class="btn btn-outline btn-primary">Nova</a>';
                $botoes = $botoes.' <a href="/categoria/lista" class="btn btn-outline btn-primary">Lista</a>';

            } catch (\Exception $e) {
                $corPainel = 'panel-red';
                $txtTitulo = 'Erro';
                $txtMensagem = 'Ocorreu um erro na gravação do registro '. $e->getMessage();
                $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            }

            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));

        }
    }

    public function exclui(Request $request) {

        try {
            /*DB::table('categorias')
                ->where('id', $idRegistro)
                ->delete();*/
            $data = $request->input();
            $idRegistro = $data['idexclui'];

            Categoria::find($idRegistro)->delete();

            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
            $botoes = '<a href="/categoria/cadastro" class="btn btn-outline btn-primary">Nova</a>';
            $botoes = $botoes.' <a href="/categoria/lista" class="btn btn-outline btn-primary">Lista</a>';

        } catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            if (strpos($e->getMessage(), 'a foreign key constraint fails (`liga_hortolandia`.`equipes`, CONSTRAINT ') > 0)
               $txtMensagem = 'Existe equipes cadastradas nesta categoria';
            else $txtMensagem = 'Ocorreu um erro na exclusão do registro '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

}
