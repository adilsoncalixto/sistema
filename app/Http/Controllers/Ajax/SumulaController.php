<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Competicao;
use App\Models\Dirigente;
use App\Models\Jogador;
use App\Models\Local;
use App\Models\Partida;
use App\Models\Sumula;
use App\Models\SumulaDirigentes;
use App\Models\SumulaEquipes;
use App\Models\SumulaJogadores;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SumulaController extends Controller
{
    /**
     * @var Partida
     */
    private $partida;
    /**
     * @var Jogador
     */
    private $jogador;
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
     * @var Competicao
     */
    private $competicao;
    /**
     * @var Dirigente
     */
    private $dirigente;
    /**
     * @var SumulaDirigentes
     */
    private $sumulaDirigentes;

    public function __construct(Competicao $competicao, Partida $partida, Jogador $jogador, Sumula $sumula, Local $local,
                                SumulaEquipes $sumulaEquipes, SumulaJogadores $sumulaJogadores, Dirigente $dirigente,
                                SumulaDirigentes $sumulaDirigentes)
    {

        $this->partida = $partida;
        $this->jogador = $jogador;
        $this->sumula = $sumula;
        $this->sumulaEquipes = $sumulaEquipes;
        $this->sumulaJogadores = $sumulaJogadores;
        $this->local = $local;
        $this->competicao = $competicao;
        $this->dirigente = $dirigente;
        $this->sumulaDirigentes = $sumulaDirigentes;
    }

    public function locais(Request $request)
    {
        $dados = $request->all();

        $competicoes = $this->competicao->listLocais($dados['competicao'])->first();

        $locais = $competicoes->locais;

        $listaLlocais = $this->local->list(explode(';',$locais))->orderByLocal()->get();

        return json_encode($listaLlocais);
    }


    public function partidas(Request $request)
    {
        $dados = $request->all();
        $idCompeticao = $dados ? $dados['competicao'] : -1;
        $idLocal      = $dados ? $dados['local']      : -1;

        $partidas = [];

        $listaPartidas = $this->partida->byCompeticaoPartida($idCompeticao, $idLocal)->get();
        foreach ($listaPartidas as $partida) {
            $partidas[] = ['id' => $partida->id, 'local' => datetime_to_string($partida->inicio) .' - '.$partida->equipe1.' X '.$partida->equipe2];
        }


        return json_encode($partidas);
    }

    public function equipes(Request $request)
    {
        $dados = $request->all();
        $idpartida = $dados ? $dados['partida'] : -1;
        $partida = $this->partida->dadosPartidas($idpartida)->first();
        return $partida;
    }

    public function dirigentesDaEquipe1(Request $request)
    {
        $dados = $request->all();
        $idReg = $dados ? $dados['equipe'] : -1;
        $listaDirigentes = $this->dirigente->byEquipe($idReg)->orderByNome()->get();
        return json_encode($listaDirigentes);
    }

    public function dirigentesDaEquipe(Request $request)
    {
        $dados = $request->all();
        $idReg = $dados ? $dados['equipe'] : -1;
        $listaDirigentes = $this->dirigente->byEquipe($idReg)->liberados()->orderByNome()->get();
        return json_encode($listaDirigentes);
    }



    public function jogadoresDaEquipe(Request $request)
    {
        $dados = $request->all();
        $idReg = $dados ? $dados['equipe'] : -1;
        if(isset($dados['categoria_id'])){
            $listaJogadores = $this->jogador->jogadoresByEquipe($idReg)->jogadorByCategoria($dados['categoria_id'])->liberados()->orderByNome()->get();
        } else {
            $listaJogadores = $this->jogador->jogadoresByEquipe($idReg)->liberados()->orderByNome()->get();
        }

        foreach ($listaJogadores as $key=> $listaJogadore) {
            $docsJogador = DB::table('jogador_documentos')->select('foto_jogador')->where('jogador_id', $listaJogadore->id)->first();
            if ($docsJogador != null) {
                if ($docsJogador->foto_jogador == null || strlen($docsJogador->foto_jogador) == 0) {
                    $fotoJogador = '/imagens/No-Image-Person.jpg';
                } else {
                    $fotoJogador = '/imagens/' . $docsJogador->foto_jogador;
                }
            }
            $listaJogadores[$key]['foto_jogador'] = $fotoJogador;
        }
        return json_encode($listaJogadores);
    }

    public function gravaPreSumula(Request $request)
    {
        $dados = $request->all();

        $dados['registros1']   = substr($dados['registros1'],   0, strlen($dados['registros1'])-1);
        $dados['registros2']   = substr($dados['registros2'],   0, strlen($dados['registros2'])-1);
        $dados['camisas1']     = substr($dados['camisas1'],     0, strlen($dados['camisas1'])-1);
        $dados['camisas2']     = substr($dados['camisas2'],     0, strlen($dados['camisas2'])-1);
        $dados['listajogar1']  = substr($dados['listajogar1'],  0, strlen($dados['listajogar1'])-1);
        $dados['listajogar2']  = substr($dados['listajogar2'],  0, strlen($dados['listajogar2'])-1);
        $dados['listaInicio1'] = substr($dados['listaInicio1'], 0, strlen($dados['listaInicio1'])-1);
        $dados['listaInicio2'] = substr($dados['listaInicio2'], 0, strlen($dados['listaInicio2'])-1);

        $dados['listadirigentes1'] = substr($dados['listadirigentes1'], 0, strlen($dados['listadirigentes1'])-1);
        $dados['listadirigentes2'] = substr($dados['listadirigentes2'], 0, strlen($dados['listadirigentes2'])-1);

        $registros1 = explode(';', $dados['registros1']);
        $registros2 = explode(';', $dados['registros2']);

        $listadirigentes1 = explode(';', $dados['listadirigentes1']);
        $listadirigentes2 = explode(';', $dados['listadirigentes2']);
     //   dd($listadirigentes1, $listadirigentes2);

        $jogadores1 = $dados['listajogar1'] ? explode(';', $dados['listajogar1']) : null;
        $jogadores2 = $dados['listajogar2'] ? explode(';', $dados['listajogar2']) : null;

        $camisas1   = explode(';', $dados['camisas1']);
        $camisas2   = explode(';', $dados['camisas2']);
        $inicia1    = explode(';', $dados['listaInicio1']);
        $inicia2    = explode(';', $dados['listaInicio2']);

        $sumula = $this->sumula->byCompeticaoPartida($dados['competicao_id'], $dados['partida_id'])->first();
        $registroSumula = [
            'competicao_id' => $dados['competicao_id'],
            'partida_id'     => $dados['partida_id'],
            'data_partida'  => implode("-",array_reverse(explode("/",$dados['data_sumula'])))
        ];

        if (!$sumula) {
            // cria a sumula.
            $this->sumula->insert($registroSumula);
            $sumula = $this->sumula->byCompeticaoPartida($dados['competicao_id'], $dados['partida_id'])->first();
        } else {
            $sumula->update($registroSumula);
        }
        $idSumula = $sumula->id;

        // grava a relacao sumula x equipes
        $registroEquipe = [
            'sumula_id' => $idSumula,
            'equipe_lado' => 1,
            'equipe_id' => $dados['equipe1_id'],
            'dirigente_id' => null
        ];

        $sumulaEquipe = $this->sumulaEquipes->byEquipe($idSumula,1)->first();
        if (!$sumulaEquipe) $this->sumulaEquipes->insert($registroEquipe);
        else $sumulaEquipe->update($registroEquipe);

        $registroEquipe = [
            'sumula_id' => $idSumula,
            'equipe_lado' => 2,
            'equipe_id' => $dados['equipe2_id'],
            'dirigente_id' => null
        ];
        $sumulaEquipe = $this->sumulaEquipes->byEquipe($idSumula,2)->first();
        if (!$sumulaEquipe) $this->sumulaEquipes->insert($registroEquipe);
        else $sumulaEquipe->update($registroEquipe);

        $regJogadores1 = null;
        $numeroDeJogadores= count($jogadores1);
        for ($i=0;$i<$numeroDeJogadores;$i++) {
            $registro =  $jogadores1[$i];
            $pos1 = array_search($registro, $registros1);
            $pos2 = in_array($registro, $inicia1);
            $ini = !$pos2 ? 0 : 1;
            $regJogadores1[] = [
                'sumula_id'  => $idSumula,
                'equipe_id'  => 1,
                'jogador_id' => $registro,
                'inicia'     => $ini,
                'camisa'     => $camisas1[$pos1],
            ];
        }

        $regJogadores2 = null;

        $numeroDeJogadores= count($jogadores2);

        for ($i=0;$i<$numeroDeJogadores;$i++) {
            $registro =  $jogadores2[$i];
            $pos1 = array_search($registro, $registros2);

            $pos2 = in_array($registro, $inicia2);
            $ini = !$pos2 ? 0 : 1;
            $regJogadores2[] = [
                'sumula_id'  => $idSumula,
                'equipe_id'  => 2,
                'jogador_id' => $registro,
                'inicia'     => $ini,
                'camisa'     => $camisas2[$pos1],
            ];
        }


        $this->sumulaJogadores->where('sumula_id',$idSumula)->where('equipe_id',1)->delete();
        $this->sumulaJogadores->where('sumula_id',$idSumula)->where('equipe_id',2)->delete();

        $this->sumulaDirigentes->where('sumula_id',$idSumula)->where('lado_equipe',1)->delete();
        $this->sumulaDirigentes->where('sumula_id',$idSumula)->where('lado_equipe',2)->delete();


        if ($regJogadores1) $this->sumulaJogadores->insert($regJogadores1);
        if ($regJogadores2) $this->sumulaJogadores->insert($regJogadores2);

        for ($i=0;$i<count($listadirigentes1);$i++) {
            $registro = $listadirigentes1[$i];
            if ($registro != null || $registro != "")
                $this->sumulaDirigentes->insert(['sumula_id' => $idSumula,'lado_equipe' => 1, 'dirigente_id' => $registro]);
        }

        for ($i=0;$i<count($listadirigentes2);$i++) {
            $registro = $listadirigentes2[$i];
            if ($registro != null || $registro != "")
                $this->sumulaDirigentes->insert(['sumula_id' => $idSumula,'lado_equipe' => 2, 'dirigente_id' => $registro]);
        }

        $resultado = [];
        $resultado['resultado'] = 'SUCESSO';
        $resultado['sumula_id'] = $idSumula;


        return json_encode($resultado);
    }
}
