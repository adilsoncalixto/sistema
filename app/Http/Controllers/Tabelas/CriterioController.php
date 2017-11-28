<?php

namespace App\Http\Controllers\Tabelas;

use App\Models\Criterio;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CriterioController extends Controller
{
    /**
     * @var Criterio
     */
    private $criterio;

    public function __construct(Criterio $criterio)
    {

        $this->criterio = $criterio;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaDeRegistros = $this->criterio->all();

        return view('criterios.index', compact('listaDeRegistros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('criterios.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dados = $request->all();
        if(array_key_exists('_token', $dados)) unset($dados['_token']);
        try {

            $this->criterio->insert($dados);

            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A gravação do registro foi realizada com sucesso';
            $botoes = '<a href="/tabelas/criterios/novo" class="btn btn-outline btn-primary">Novo</a>';
            $botoes = $botoes.' <a href="/tabelas/criterios" class="btn btn-outline btn-primary">Lista</a>';


        }  catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na gravação do registro '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $criterio = $this->criterio->find($id);

        return view('criterios.edit', compact('criterio'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dados = $request->all();
        $registro = $this->criterio->find($id);
        if ($registro) {
            try {

                $registro->update($dados);

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A gravação do registro foi realizada com sucesso';
                $botoes = '<a href="/tabelas/criterios/novo" class="btn btn-outline btn-primary">Novo</a>';
                $botoes = $botoes.' <a href="/tabelas/criterios" class="btn btn-outline btn-primary">Lista</a>';


            } catch (\Exception $e) {
                $corPainel = 'panel-red';
                $txtTitulo = 'Erro';
                $txtMensagem = 'Ocorreu um erro na gravação do registro '. $e->getMessage();
                $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            }

            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $registro = $this->criterio->find($id);
        if ($registro) {
            try {

                $registro->delete();

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
                $botoes = '<a href="/tabelas/criterios/novo" class="btn btn-outline btn-primary">Novo</a>';
                $botoes = $botoes.' <a href="/tabelas/criterios" class="btn btn-outline btn-primary">Lista</a>';


            } catch (\Exception $e) {
                $corPainel = 'panel-red';
                $txtTitulo = 'Erro';
                $txtMensagem = 'Ocorreu um erro na exclusão do registro '. $e->getMessage();
                $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            }

            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));

        }
    }
}
