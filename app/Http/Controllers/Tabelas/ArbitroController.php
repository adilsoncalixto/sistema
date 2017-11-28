<?php

namespace App\Http\Controllers\Tabelas;

use App\Models\Arbitro;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ArbitroController extends Controller
{

    /**
     * @var Arbitro
     */
    private $arbitro;

    public function __construct(Arbitro $arbitro)
    {

        $this->arbitro = $arbitro;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaDeRegistros = $this->arbitro->all();

        return view('arbitros.index', compact('listaDeRegistros'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('arbitros.create');
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

            $this->arbitro->insert($dados);

            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A gravação do registro foi realizada com sucesso';
            $botoes = '<a href="/tabelas/arbitros/novo" class="btn btn-outline btn-primary">Novo</a>';
            $botoes = $botoes.' <a href="/tabelas/arbitros" class="btn btn-outline btn-primary">Lista</a>';


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
        $arbitro = $this->arbitro->find($id);

        return view('arbitros.edit', compact('arbitro'));

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
        $registro = $this->arbitro->find($id);
        if ($registro) {
            try {

                $registro->update($dados);

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A gravação do registro foi realizada com sucesso';
                $botoes = '<a href="/tabelas/arbitros/novo" class="btn btn-outline btn-primary">Novo</a>';
                $botoes = $botoes.' <a href="/tabelas/arbitros" class="btn btn-outline btn-primary">Lista</a>';


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
        $registro = $this->arbitro->find($id);
        if ($registro) {
            try {

                $registro->delete();

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
                $botoes = '<a href="/tabelas/arbitros/novo" class="btn btn-outline btn-primary">Novo</a>';
                $botoes = $botoes.' <a href="/tabelas/arbitros" class="btn btn-outline btn-primary">Lista</a>';


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
