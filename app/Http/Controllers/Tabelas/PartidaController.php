<?php

namespace App\Http\Controllers\Tabelas;

use App\Models\Arbitro;
use App\Models\Competicao;
use App\Models\Equipe;
use App\Models\Jogador;
use App\Models\Local;
use App\Models\Partida;
use App\Models\Categoria;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PartidaController extends Controller
{

    /**
     * @var Partida
     */
    private $partida;
    /**
     * @var Local
     */
    private $local;
    /**
     * @var Arbitro
     */
    private $arbitro;
    /**
     * @var Equipe
     */
    private $equipe;
    /**
     * @var Competicao
     */
    private $competicao;
    /**
     * @var Jogador
     */
    private $jogador;
    /**
     * @var Categoria
     */
    private $categoria;

    public function __construct(Categoria $categoria, Competicao $competicao, Partida $partida, Local $local, Arbitro $arbitro,
                                Equipe $equipe, Jogador $jogador)
    {

        $this->partida = $partida;
        $this->local = $local;
        $this->arbitro = $arbitro;
        $this->equipe = $equipe;
        $this->competicao = $competicao;
        $this->jogador = $jogador;
        $this->categoria = $categoria;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaDeRegistros = $this->partida->ListAll()->get();


        return view('partidas.index', compact('listaDeRegistros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $competicoes =  array('0' => 'Selecione') + $this->competicao->lists('nome', 'id')->toArray();
        $locais =  array('0' => 'Selecione') + $this->local->lists('nome', 'id')->toArray();
        $arbitros =  array('0' => 'Selecione') + $this->arbitro->lists('nome', 'id')->toArray();
        $equipes =  array('0' => 'Selecione') + $this->equipe->lists('nome_equipe', 'id')->toArray();
        $categorias = array('0' => 'Selecione') + $this->categoria->orderBy('nome_categoria')->lists('nome_categoria','id')->toArray();


        $competicaoSelecionada = 0;
        $localSelecionado = 0;
        $arbitroSelecionado1 = 0;
        $arbitroSelecionado2 = 0;
        $arbitroSelecionado3 = 0;
        $equipeSelecionada1 = 0;
        $equipeSelecionada2 = 0;
        $rodada = 0;

        $categoria = -1;

        $hrinicio =  null;
        $hrtermino =  null;
        $datapartida =  null;
        $vencedor = null;
        $historico = null;

        return view('partidas.create', compact('categorias','categoria','competicoes','locais','competicaoSelecionada','datapartida','hrinicio',
            'localSelecionado','arbitros','arbitroSelecionado1','arbitroSelecionado2','arbitroSelecionado3','equipes',
            'equipeSelecionada1','equipeSelecionada2','hrtermino','vencedor', 'historico', 'rodada'));
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
        $dados['inicio'] = implode("-",array_reverse(explode("/",$request->input('datapartida')))).' '.$dados['hrinicio'].':00';
        $dados['termino'] = implode("-",array_reverse(explode("/",$request->input('datapartida')))).' '.$dados['hrtermino'].':00';
        if(array_key_exists('datapartida', $dados)) unset($dados['datapartida']);
        if(array_key_exists('hrinicio', $dados)) unset($dados['hrinicio']);
        if(array_key_exists('hrtermino', $dados)) unset($dados['hrtermino']);

//        $jogador = $this->jogador->jogadoresByEquipe($dados['equipe1_id'])->first();
//        if ($jogador)
//            $dados['categoria_id'] = $jogador->categoria_id;
//        else
//            $dados['categoria_id'] = null;

        $validaPartida = $this->partida->validaLocalHorario($dados['local_id'],$dados['inicio'])->first();

        if ($validaPartida) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Já existe uma partida cadatrada para este local e data/horario';
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        }

        try {

            $this->partida->insert($dados);

            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A gravação do registro foi realizada com sucesso';
            $botoes = '<a href="/tabelas/partidas/novo" class="btn btn-outline btn-primary">Novo</a>';
            $botoes = $botoes.' <a href="/tabelas/partidas" class="btn btn-outline btn-primary">Lista</a>';


        }  catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na gravação do registro '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));//
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $partida = $this->partida->find($id);


        $competicoes =  array('0' => 'Selecione') + $this->competicao->lists('nome', 'id')->toArray();
        $locais =  array('0' => 'Selecione') + $this->local->lists('nome', 'id')->toArray();
        $arbitros =  array('0' => 'Selecione') + $this->arbitro->lists('nome', 'id')->toArray();
        $equipes =  $this->equipe->lists('nome_equipe', 'id')->toArray();

        $categorias = array('0' => 'Selecione') + $this->categoria->orderBy('nome_categoria')->lists('nome_categoria','id')->toArray();


        $competicaoSelecionada = $partida->competicao_id;
        $localSelecionado      = $partida->local_id;
        $arbitroSelecionado1   = $partida->arbitro1_id;
        $arbitroSelecionado2   = $partida->arbitro2_id;
        $arbitroSelecionado3   = $partida->arbitro3_id;
        $equipeSelecionada1    = $partida->aequipe1_id;
        $equipeSelecionada2    = $partida->aequipe2_id;
        $categoria    = $partida->categoria_id;
        $rodada    = $partida->rodada;

        $hrinicio =  date_format(date_create($partida->inicio),"H:i");
        $hrtermino =  date_format(date_create($partida->termino),"H:i");
        $datapartida =  date_format(date_create(substr($partida->inicio,0,10)  ),"d/m/Y");
        $vencedor = $partida->vencedor;
        $historico = $partida->historico;

        return view('partidas.edit', compact('categorias','categoria','competicoes','locais','competicaoSelecionada','partida','datapartida',
            'localSelecionado','arbitros','arbitroSelecionado1','arbitroSelecionado2','arbitroSelecionado3','equipes',
            'equipeSelecionada1','equipeSelecionada2','vencedor', 'historico','hrinicio', 'hrtermino', 'rodada'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
        if(array_key_exists('_token', $dados)) unset($dados['_token']);
        $dados['inicio'] = implode("-",array_reverse(explode("/",$request->input('datapartida')))).' '.$dados['hrinicio'].':00';
        $dados['termino'] = implode("-",array_reverse(explode("/",$request->input('datapartida')))).' '.$dados['hrtermino'].':00';
        if(array_key_exists('datapartida', $dados)) unset($dados['datapartida']);
        if(array_key_exists('hrinicio', $dados)) unset($dados['hrinicio']);
        if(array_key_exists('hrtermino', $dados)) unset($dados['hrtermino']);


        try {

            $registro =$this->partida->find($id);

            if(!$registro) {
                $corPainel = 'panel-red';
                $txtTitulo = 'Erro';
                $txtMensagem = 'Registro não encontrado';
                $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
                return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
            }

//            $jogador = $this->jogador->jogadoresByEquipe($dados['equipe1_id'])->first();
//            if ($jogador)
//                $dados['categoria_id'] = $jogador->categoria_id;
//            else
//                $dados['categoria_id'] = null;

            $registro->update($dados);

            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A gravação do registro foi realizada com sucesso';
            $botoes = '<a href="/tabelas/partidas/novo" class="btn btn-outline btn-primary">Novo</a>';
            $botoes = $botoes.' <a href="/tabelas/partidas" class="btn btn-outline btn-primary">Lista</a>';


        }  catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na gravação do registro '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $registro = $this->partida->find($id);
        if ($registro) {
            try {

                $registro->delete();

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
                $botoes = '<a href="/tabelas/partidas/novo" class="btn btn-outline btn-primary">Nova</a>';
                $botoes = $botoes.' <a href="/tabelas/partidas" class="btn btn-outline btn-primary">Lista</a>';


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
