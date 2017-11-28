<?php

namespace App\Http\Controllers\Tabelas;

use App\Models\Arbitro;
use App\Models\Categoria;
use App\Models\Competicao;
use App\Models\Dirigente;
use App\Models\Equipe;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Partida;
use App\Models\Sumula;
use App\Models\SumulaEquipes;
use App\Models\SumulaJogadores;
use App\Models\HistoricoPartida;
use App\Services\SumulaService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Vsmoraes\Pdf\Pdf;

class SumulaController extends Controller
{
    /**
     * @var Competicao
     */
    private $competicao;
    /**
     * @var Partida
     */
    private $partida;
    /**
     * @var Categoria
     */
    private $categoria;
    /**
     * @var Arbitro
     */
    private $arbitro;
    /**
     * @var Dirigente
     */
    private $dirigente;
    /**
     * @var Sumula
     */
    private $sumula;
    /**
     * @var SumulaEquipes
     */
    private $sumulaEquipes;
    /**
     * @var SumulaJogadores
     */
    private $sumulaJogadores;
    /**
     * @var Local
     */
    private $local;
    /**
     * @var Pdf
     */
    private $pdf;
    /**
     * @var Equipe
     */
    private $equipe;
    /**
     * @var SumulaService
     */
    private $sumulaService;
    /**
     * @var SumulaService
     */
    private $historicoPartida;

    public function __construct(
            Competicao $competicao,
            Partida $partida,
            Local $local,
            Categoria $categoria,
            Equipe $equipe,
            Arbitro $arbitro,
            Dirigente $dirigente,
            Sumula $sumula,
            SumulaEquipes $sumulaEquipes,
            SumulaJogadores $sumulaJogadores,
            Pdf $pdf,
            SumulaService $sumulaService,
            HistoricoPartida $historicoPartida
        )
    {

        $this->competicao = $competicao;
        $this->partida = $partida;
        $this->categoria = $categoria;
        $this->arbitro = $arbitro;
        $this->dirigente = $dirigente;
        $this->sumula = $sumula;
        $this->sumulaEquipes = $sumulaEquipes;
        $this->sumulaJogadores = $sumulaJogadores;
        $this->local = $local;
        $this->pdf = $pdf;
        $this->equipe = $equipe;
        $this->sumulaService = $sumulaService;
        $this->historicoPartida = $historicoPartida;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaSumulas = $this->sumula->lista()->get();

        return view('sumulas.index', compact('listaSumulas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data_sumula = date('d/m/Y');
        $partidas    = array('-1' => "Selecione");
        $competicoes = array('-1' => 'Selecione') + $this->competicao->orderBy('nome')->lists('nome', 'id')->toArray();
        $categorias  = array('-1' => 'Selecione') + $this->categoria->orderBy('nome_categoria')->lists('nome_categoria', 'id')->toArray();
        $arbitros1   = array('-1' => 'Selecione') + $this->arbitro->orderBy('nome')->lists('nome', 'id')->toArray();
        $arbitros2   = array('-1' => 'Selecione') + $this->arbitro->orderBy('nome')->lists('nome', 'id')->toArray();
        $arbitros3   = array('-1' => 'Selecione') + $this->arbitro->orderBy('nome')->lists('nome', 'id')->toArray();
        $dirigentes  = array('-1' => 'Selecione') + $this->dirigente->orderBy('nome_dirigente')->lists('nome_dirigente', 'id')->toArray();
        return view('sumulas.create', compact('data_sumula','competicoes','partidas','categorias',
            'arbitros1','arbitros2','arbitros3', 'dirigentes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sumula = $this->sumula->find($id);
        if ($sumula){
            $rodada = $this->partida->find($sumula->partida_id)->rodada;
            //$numrodada = $rodada->rodada;
            $data_sumula = date_to_string($sumula->data_partida);
            $competicaoSelecionada = $sumula->competicao_id;
            $partidaSelecionada    = $sumula->partida_id;

            $tabelaCompeticao = $this->competicao->find($competicaoSelecionada);
            if ($tabelaCompeticao) {
                $nomeCompeticao = $tabelaCompeticao->nome;
            } else $nomeCompeticao = null;

            $tabelaPartida    = $this->partida->find($partidaSelecionada);

            if ($tabelaPartida) {
                $dataPartida  = datetime_to_string($tabelaPartida->inicio);
                $equipe1_id   = $tabelaPartida->equipe1_id;
                $equipe2_id   = $tabelaPartida->equipe2_id;
                $local_id     = $tabelaPartida->local_id;
                $categoria_id = $tabelaPartida->categoria_id;

                $arbitro1_id = $tabelaPartida->arbitro1_id;
                $arbitro2_id = $tabelaPartida->arbitro2_id;
                $arbitro3_id = $tabelaPartida->arbitro3_id;


                $tabelaLocal = $this->local->find($local_id);
                if ($tabelaLocal) $nomeLocal = $tabelaLocal->nome;
                else $nomeLocal = null;
            } else {
                $dataPartida = null;
                $equipe1_id  = null;
                $equipe2_id  = null;
                $nomeLocal   = null;
                $categoria_id = null;
            }

            if ($equipe1_id) {

                $tabelaEquipe = $this->equipe->find($equipe1_id);

                if ($tabelaEquipe) {
                    $nomeEquipe1 = $tabelaEquipe->nome_equipe;

                    $tabelaDirigente = $this->dirigente->where('equipe_id','=',$equipe1_id)->first();
                    if ($tabelaDirigente) $dirigente1 = $tabelaDirigente->id;
                    else $dirigente1 = null;

                    $tabelaCategoria = $this->categoria->find($categoria_id);
                    if ($tabelaCategoria) {
                        $nomeCategoria = $tabelaCategoria->nome_categoria;
                    } else $nomeCategoria = null;
                    $listaJogadores1 = $this->sumulaJogadores->select('sumula_jogadores.*','jogadores.nome_jogador')
                        ->where('sumula_id',$id)
                        ->where('sumula_jogadores.equipe_id',1)
                        ->join('jogadores','jogadores.id','=', 'sumula_jogadores.jogador_id')
                        ->orderBy('jogadores.nome_jogador')
                        ->get();
                    $sumulaEquipe = $this->sumulaEquipes->where('sumula_id','=', $id)->where('equipe_lado','=',1)->where('equipe_id','=',$equipe1_id)->first();
                    if ($sumulaEquipe) {
                        $massagista1 = $sumulaEquipe->massagista;
                        $dirigente1 = $sumulaEquipe->dirigente_id;
                      //  dd($dirigente1);
                    } else {
                        $massagista1 = null;
                    }

                }
                else  {
                    $nomeEquipe1 = null;
                    $listaJogadores1 = null;
                    $massagista1 = null;
                }
            } else {
                $nomeEquipe1 = null;
                $listaJogadores1 = null;
            }

            if ($equipe2_id) {
                $tabelaEquipe = $this->equipe->find($equipe2_id);
                if ($tabelaEquipe) {
                    $nomeEquipe2 = $tabelaEquipe->nome_equipe;

                    $tabelaDirigente = $this->dirigente->where('equipe_id','=',$equipe2_id)->first();
                    if ($tabelaDirigente) $dirigente2 = $tabelaDirigente->id;
                    else $dirigente2 = null;
                    $listaJogadores2 = $this->sumulaJogadores->select('sumula_jogadores.*','jogadores.nome_jogador')
                        ->where('sumula_id',$id)
                        ->where('sumula_jogadores.equipe_id',2)
                        ->join('jogadores','jogadores.id','=', 'sumula_jogadores.jogador_id')
                        ->orderBy('jogadores.nome_jogador')
                        ->get();
                    $sumulaEquipe = $this->sumulaEquipes->where('sumula_id','=', $id)->where('equipe_lado','=',2)->where('equipe_id','=',$equipe2_id)->first();
                    if ($sumulaEquipe) {
                        $massagista2 = $sumulaEquipe->massagista;
                        $dirigente2 = $sumulaEquipe->dirigente_id;
                    } else {
                        $massagista2 = null;
                    }
                }
                else {
                    $nomeEquipe2 = null;
                    $listaJogadores2 = null;
                    $massagista2 = null;
                }
            } else {
                $nomeEquipe2 = null;
                $listaJogadores2 = null;
            }

            $arbitros1   = array('-1' => 'Selecione') + $this->arbitro->orderBy('nome')->lists('nome', 'id')->toArray();
            $arbitros2   = array('-1' => 'Selecione') + $this->arbitro->orderBy('nome')->lists('nome', 'id')->toArray();
            $arbitros3   = array('-1' => 'Selecione') + $this->arbitro->orderBy('nome')->lists('nome', 'id')->toArray();
            $dirigentes1 = array('-1' => 'Selecione') + $this->dirigente->where('equipe_id',$equipe1_id)->orderBy('nome_dirigente')->lists('nome_dirigente', 'id')->toArray();
            $dirigentes2 = array('-1' => 'Selecione') + $this->dirigente->where('equipe_id',$equipe2_id)->orderBy('nome_dirigente')->lists('nome_dirigente', 'id')->toArray();
            $dirigentes  = array('-1' => 'Selecione') + $this->dirigente->orderBy('nome_dirigente')->lists('nome_dirigente', 'id')->toArray();


            $placarVencedor =  $this->sumulaService->calculaPlacar($id);
          //  dd($placarVencedor);
            $placarVencedor = explode(';', $placarVencedor);
            
            $gols_eq1 = $this->historicoPartida->countGolsEquipe($equipe1_id, $sumula->id);
            $gols_eq2 = $this->historicoPartida->countGolsEquipe($equipe2_id, $sumula->id);
            $placarEquipe1 = 0;
            $placarEquipe2 = 0;
            if(count($gols_eq1) > 0){
                $placarEquipe1 = $gols_eq1[0]->GF + $gols_eq2[0]->GC;
            }
            if(count($gols_eq2) > 0){
                $placarEquipe2 = $gols_eq2[0]->GF + $gols_eq1[0]->GC;
            }
            $idVencedor    = $placarVencedor[2];
            $nomeVencedor  = $placarVencedor[3];

            $listaFaltas = array('0' => '0') + array('1' => '1') + array('2' => '2') + array('3' => '3') +
                    array('4' => '4') + array('5' => '5') + array('6' => '6');

        }


        return view('sumulas.edit', compact('sumula', 'data_sumula','nomeCompeticao','nomeLocal','dataPartida',
            'local_id', 'nomeEquipe1', 'nomeEquipe2', 'equipe1_id','equipe2_id','nomeCategoria','arbitros1','arbitros2',
            'arbitros3', 'dirigentes', 'categoria_id', 'listaJogadores1', 'listaJogadores2', 'arbitro1_id','arbitro2_id',
            'arbitro3_id','dirigente1', 'dirigente2','massagista1', 'massagista2', 'dirigentes1','dirigentes2',
            'placarEquipe1','placarEquipe2','idVencedor', 'nomeVencedor','listaFaltas', 'rodada'));
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

        //dd($dados);

        $sumula = $this->sumula->find($id);

        if ($sumula) {

            $registroSumula = [
                'serie'              => $dados['serie'],
                'fase'               => $dados['fase'],
                'anotador'           => $dados['anotador'],
                'cronometrista'      => $dados['cronometrista'],
                'periodo_1_inicio'   => $dados['periodo_1_inicio'],
                'periodo_1_termino'  => $dados['periodo_1_termino'],
                'periodo_2_inicio'   => $dados['periodo_2_inicio'],
                'periodo_2_termino'  => $dados['periodo_2_termino'],
                'periodo_3_inicio'   => $dados['periodo_3_inicio'],
                'periodo_3_termino'  => $dados['periodo_3_termino'],
                'periodo_4_inicio'   => $dados['periodo_4_inicio'],
                'periodo_4_termino'  => $dados['periodo_4_termino'],
                'marcador_1_equipe1' => $dados['marcador_1_equipe1'],
                'marcador_1_equipe2' => $dados['marcador_1_equipe2'],
                'marcador_2_equipe1' => $dados['marcador_2_equipe1'],
                'marcador_2_equipe2' => $dados['marcador_2_equipe2'],
                'marcador_3_equipe1' => $dados['marcador_3_equipe1'],
                'marcador_3_equipe2' => $dados['marcador_3_equipe2'],
                'marcador_4_equipe1' => $dados['marcador_4_equipe1'],
                'marcador_4_equipe2' => $dados['marcador_4_equipe2']
            ];

            $sumula->update($registroSumula);

            $partida = $this->partida->find($dados['partida_id']);

            if ($partida) {
                $registroPartida = [
                    'arbitro1_id' => $dados['arbitro1_id'],
                    'arbitro2_id' => $dados['arbitro2_id'],
                    'arbitro3_id' => $dados['arbitro3_id']
                ];

                $partida->update($registroPartida);
            }
            
            $sumulaEquipe = $this->sumulaEquipes->where('sumula_id','=', $id)->where('equipe_lado','=',1)->where('equipe_id','=',$dados['equipe1_id'])->first();
            if ($sumulaEquipe) {
                $registro = [
                    'massagista' => $dados['massagista1']
                ];
                $sumulaEquipe->update($registro);
            }

            $sumulaEquipe = $this->sumulaEquipes->where('sumula_id','=', $id)->where('equipe_lado','=',2)->where('equipe_id','=',$dados['equipe2_id'])->first();
            if ($sumulaEquipe) {
                $registro = [
                    'massagista' => $dados['massagista2']
                ];
                $sumulaEquipe->update($registro);
            }

            if (isset($dados['registro1'])) {
                for ($i=0;$i<count($dados['registro1']);$i++) {

                    $sumulaJogador = $this->sumulaJogadores->where('sumula_id','=', $id)
                        ->where('equipe_id','=',1)
                        ->where('jogador_id','=',$dados['registro1'][$i])->first();
                    if ($sumulaJogador) {

                        $registroJogador = [
                            'gols'     => $dados['gols1'][$i],
                            'faltas'   => isset($dados['faltas1']) ? $dados['faltas1'][$i] : null,
                            'amarelo'  => $dados['amarelo1'][$i],
                            'vermelho' => $dados['vermelho1'][$i]
                        ];
                        $sumulaJogador->update($registroJogador);
                    }
                }
            }

            if (isset($dados['registro2'])) {
                for ($i=0;$i<count($dados['registro2']);$i++) {

                    $sumulaJogador = $this->sumulaJogadores->where('sumula_id','=', $id)
                        ->where('equipe_id','=',2)
                        ->where('jogador_id','=',$dados['registro2'][$i])->first();
                    if ($sumulaJogador) {
                        $registroJogador = [
                            'gols'     => $dados['gols2'][$i],
                            'faltas'   => isset($dados['faltas2']) ? $dados['faltas2'][$i] : null,
                            'amarelo'  => $dados['amarelo2'][$i],
                            'vermelho' => $dados['vermelho2'][$i]
                        ];
                        $sumulaJogador->update($registroJogador);
                    }
                }
            }

            $sumulaEquipe = $this->sumulaEquipes->where('sumula_id','=', $id)->where('equipe_lado','=',1)->where('equipe_id','=',$dados['equipe1_id'])->first();
            if ($sumulaEquipe) {
                $sumulaEquipe->update(['dirigente_id' => $dados['dirigente1_id']]);
            }

            $sumulaEquipe = $this->sumulaEquipes->where('sumula_id','=', $id)->where('equipe_lado','=',2)->where('equipe_id','=',$dados['equipe2_id'])->first();
            if ($sumulaEquipe) {
                $sumulaEquipe->update(['dirigente_id' => $dados['dirigente2_id']]);
            }

            $sumulaEquipe = $this->sumulaEquipes->where('sumula_id','=', $id)->where('equipe_lado','=',1)->where('equipe_id','=',$dados['equipe1_id'])->first();
            if ($sumulaEquipe) {
                $sumulaEquipe->update(['faltas' => $dados['falta_eq1']]);
            }

            $sumulaEquipe = $this->sumulaEquipes->where('sumula_id','=', $id)->where('equipe_lado','=',2)->where('equipe_id','=',$dados['equipe2_id'])->first();
            if ($sumulaEquipe) {
                $sumulaEquipe->update(['faltas' => $dados['falta_eq2']]);
            }
        }

        Session::flash('message1', 'Gravação Realizada com sucesso');

        $redir = asset('tabelas/sumulas/editar/').'/'.$id;
        return redirect($redir);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function presumula()
    {
        $data_sumula = date('d/m/Y');
        $partidas    = array('-1' => "Selecione");
        $competicoes = array('-1' => 'Selecione') + $this->competicao->orderBy('nome')->lists('nome', 'id')->toArray();
        $locais  = array('-1' => 'Selecione');// + $this->local->orderBy('nome')->lists('nome', 'id')->toArray();
        return view('sumulas.pre-sumula', compact('data_sumula','competicoes','locais','partidas'));
    }

    public function pdf($id)
    {
        $pathImagem = Parametro::where('name','path_imagens')->first()->value;
        $pathImagem = public_path('imagens').'/';

        $logo = $pathImagem.'logo.png';
        $logo = $this->converimaget($logo);

        $sumula = $this->sumula->lista()->where('sumulas.id','=',$id)->first();

        $equipe1 = $this->sumulaEquipes->where('sumula_id',$sumula->id)
            ->where('equipe_lado',1)
            ->join('equipes','equipes.id','=','sumula_equipes.equipe_id')
            ->first();
        $equipe2 = $this->sumulaEquipes->where('sumula_id',$sumula->id)
            ->where('equipe_lado',2)
            ->join('equipes','equipes.id','=','sumula_equipes.equipe_id')
            ->first();

        $jogadoresEquipe1 = $this->sumulaJogadores->select('sumula_jogadores.*','jogadores.nome_jogador')
            ->where('sumula_id',$sumula->id)
            ->where('sumula_jogadores.equipe_id',1)
            ->join('jogadores','jogadores.id','=', 'sumula_jogadores.jogador_id')
            ->orderBy('jogadores.nome_jogador')
            ->get();

        $jogadoresEquipe2 = $this->sumulaJogadores->select('sumula_jogadores.*','jogadores.nome_jogador')
            ->where('sumula_id',$sumula->id)
            ->where('sumula_jogadores.equipe_id',2)
            ->join('jogadores','jogadores.id','=', 'sumula_jogadores.jogador_id')
            ->orderBy('jogadores.nome_jogador')
            ->get();

        $ind = 0;
        $iniciantes1 = ['','','','',''];
        foreach ($jogadoresEquipe1 as $jog) {
            if ($jog->inicia == 1) {
                $iniciantes1[$ind] = $jog->camisa;
                $ind++;
            }
        }

        $ind = 0;
        $iniciantes2 = ['','','','',''];
        foreach ($jogadoresEquipe2 as $jog) {
            if ($jog->inicia == 1) {
                $iniciantes2[$ind] = $jog->camisa;
                $ind++;
            }
        }


        $competicaoSelecionada = $sumula->competicao_id;
        $partidaSelecionada    = $sumula->partida_id;

        $tabelaCompeticao = $this->competicao->find($competicaoSelecionada);
        if ($tabelaCompeticao) {
            $nomeCompeticao = $tabelaCompeticao->nome;
        } else $nomeCompeticao = null;

        $tabelaPartida    = $this->partida->find($partidaSelecionada);

        if ($tabelaPartida) {
            $dataPartida = datetime_to_string($tabelaPartida->inicio);
            $equipe1_id = $tabelaPartida->equipe1_id;
            $equipe2_id = $tabelaPartida->equipe2_id;
            $local_id = $tabelaPartida->local_id;

            $historico_equipe1 = $this->historicoPartida
            ->where('id_sumula',$sumula->id)
            ->where('id_equipe', $equipe1_id)
            ->get();

            $historico_equipe2 = $this->historicoPartida
            ->where('id_sumula',$sumula->id)
            ->where('id_equipe', $equipe2_id)
            ->get();

            $tblArbitro = $this->arbitro->find($tabelaPartida->arbitro1_id);
            if ($tblArbitro)
                $arbitro1 = $tblArbitro->nome;
            else
                $arbitro1 = '';

            $tblArbitro = $this->arbitro->find($tabelaPartida->arbitro2_id);
            if ($tblArbitro)
                $arbitro2 = $tblArbitro->nome;
            else
                $arbitro2 = '';

            $tblArbitro = $this->arbitro->find($tabelaPartida->arbitro3_id);
            if ($tblArbitro)
                $arbitro3 = $tblArbitro->nome;
            else
                $arbitro3 = '';

        }

        $tblDirigente = $this->dirigente->find($equipe1->dirigente_id);
        if ($tblDirigente)
            $dirigente1 = $tblDirigente->nome_dirigente;
        else
            $dirigente1 = null;

        $tblDirigente = $this->dirigente->find($equipe2->dirigente_id);
        if ($tblDirigente)
            $dirigente2 = $tblDirigente->nome_dirigente;
        else
            $dirigente2 = null;

        $gols_eq1 = $this->historicoPartida->countGolsEquipe($equipe1_id, $sumula->id);
        $gols_eq2 = $this->historicoPartida->countGolsEquipe($equipe2_id, $sumula->id);

        $placarEquipe1 = 0;
        $placarEquipe2 = 0;
        if(count($gols_eq1) > 0){
            $placarEquipe1 = $gols_eq1[0]->GF + $gols_eq2[0]->GC;
        }
        if(count($gols_eq2) > 0){
            $placarEquipe2 = $gols_eq2[0]->GF + $gols_eq1[0]->GC;
        }
        
        $html = view('relatorios.sumulas.sumula', compact('logo','sumula', 'equipe1', 'equipe2',
            'jogadoresEquipe1','jogadoresEquipe2', 'arbitro1','arbitro2','arbitro3', 'iniciantes1',
                'iniciantes2','dirigente1','dirigente2', 'historico_equipe1', 'historico_equipe2', 'placarEquipe1', 'placarEquipe2'));
        return $html;
        
    }

    function converimaget($img)
    {
        $path = $img;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function savehistorico(Request $request){
        $data = $request->all();
        
        $id = $data['id_jogador'];
        $tipeform = $data['type_form'];
        if($data['flag'] == 'save'){
            if(empty($data['id_sub'])){
                unset($data['id_sub']);
            }
            if(empty($data['id_categoria'])){
                unset($data['id_categoria']);
            }
            if(empty($data['tempo'])){
                $data['tempo'] = '00:00';
            }
            $this->historicoPartida->create($data);
        }
        if($tipeform == 'Sub'){
            $historico = $this->historicoPartida->sub($tipeform, $id);
        } else {
            $historico = $this->historicoPartida->select('tempo', 'type_form', 'id')->where('id_jogador','=', $id)->where('type_form','=', $tipeform)->get();
        }

        return response()->json( $historico, 200);
    }

     public function deletahistorico(Request $request){
       
        $field = $this->historicoPartida->find($request->id);
        $field->delete();
        return response()->json( '', 200);
    }

}
