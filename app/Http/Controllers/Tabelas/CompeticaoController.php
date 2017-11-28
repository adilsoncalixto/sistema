<?php

namespace App\Http\Controllers\Tabelas;

use App\Models\Competicao;
use App\Models\Local;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CompeticaoController extends Controller
{

    /**
     * @var Local
     */
    private $local;
    /**
     * @var Competicao
     */
    private $competicao;

    public function __construct(Local $local, Competicao $competicao)
    {

        $this->local = $local;
        $this->competicao = $competicao;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaDeRegistros = $this->competicao->all();

        return view('competicoes.index', compact('listaDeRegistros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locais = $this->local->lists('nome', 'id')->toArray();
        $locaisSelecionados = 0;
        return view('competicoes.create', compact('locais','locaisSelecionados'));
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
        $locais = '';
        foreach ($dados['locais'] as $row) {
            $locais = $locais. $row .';';
        }
        $dados['locais'] = substr($locais,0,strlen($locais)-1);

        $dados['inicio'] = implode("-",array_reverse(explode("/",$request->input('inicio'))));
        $dados['termino']  = implode("-",array_reverse(explode("/",$request->input('termino'))));


        try {

            $this->competicao->insert($dados);

            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A gravação do registro foi realizada com sucesso';
            $botoes = '<a href="/tabelas/competicoes/novo" class="btn btn-outline btn-primary">Novo</a>';
            $botoes = $botoes.' <a href="/tabelas/competicoes" class="btn btn-outline btn-primary">Lista</a>';


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
        $locais = $this->local->lists('nome', 'id')->toArray();

        $competicao = $this->competicao->find($id);

        $competicao->inicio =  date_format(date_create($competicao->inicio),"d/m/Y");
        $competicao->termino =  date_format(date_create($competicao->termino),"d/m/Y");

        $locaisSelecionados = explode(';',$competicao->locais);

      //  dd($locaisSelecionados, $competicao);

        return view('competicoes.edit', compact('competicao','locais', 'locaisSelecionados'));
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
        $locais = '';
        foreach ($dados['locais'] as $row) {
            $locais = $locais. $row .';';
        }
        $dados['locais'] = substr($locais,0,strlen($locais)-1);

        $dados['inicio'] = implode("-",array_reverse(explode("/",$request->input('inicio'))));
        $dados['termino']  = implode("-",array_reverse(explode("/",$request->input('termino'))));

        $registro = $this->competicao->find($id);
        if ($registro) {
            try {

                $registro->update($dados);

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A gravação do registro foi realizada com sucesso';
                $botoes = '<a href="/tabelas/competicoes/novo" class="btn btn-outline btn-primary">Nova</a>';
                $botoes = $botoes.' <a href="/tabelas/competicoes" class="btn btn-outline btn-primary">Lista</a>';


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
        $registro = $this->competicao->find($id);
        if ($registro) {
            try {

                $registro->delete();

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
                $botoes = '<a href="/tabelas/competicoes/novo" class="btn btn-outline btn-primary">Novo</a>';
                $botoes = $botoes.' <a href="/tabelas/competicoes" class="btn btn-outline btn-primary">Lista</a>';


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
