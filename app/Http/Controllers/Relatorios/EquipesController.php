<?php

namespace App\Http\Controllers\Relatorios;

use App\Models\Competicao;
use App\Models\Dirigente;
use App\Models\Equipe;
use App\Models\Jogador;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Partida;
use App\Models\Sumula;
use App\Models\SumulaEquipes;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Vsmoraes\Pdf\Pdf;

class EquipesController extends Controller
{
    /**
     * @var Equipe
     */
    private $equipe;
    /**
     * @var Jogador
     */
    private $jogador;
    /**
     * @var Pdf
     */
    private $pdf;
    /**
     * @var Competicao
     */
    private $competicao;
    /**
     * @var Partida
     */
    private $partida;
    /**
     * @var Local
     */
    private $local;
    /**
     * @var Sumula
     */
    private $sumula;
    /**
     * @var SumulaEquipes
     */
    private $sumulaEquipes;
    /**
     * @var Dirigente
     */
    private $dirigente;

    public function __construct(Equipe $equipe, Jogador $jogador, Pdf $pdf, Sumula $sumula, SumulaEquipes $sumulaEquipes,
                                Competicao $competicao, Partida $partida, Local $local, Dirigente $dirigente)
    {

        $this->equipe = $equipe;
        $this->jogador = $jogador;
        $this->pdf = $pdf;
        $this->competicao = $competicao;
        $this->partida = $partida;
        $this->local = $local;
        $this->sumula = $sumula;
        $this->sumulaEquipes = $sumulaEquipes;
        $this->dirigente = $dirigente;
    }

    public function index()
    {
        $listaEquipes = array('-1' => 'Selecione') + $this->equipe->orderBy('nome_equipe')->lists('nome_equipe', 'id')->toArray();

        return view('relatorios.equipes.tela',compact('listaEquipes'));
    }

    public function equipeJogadores(Request $request)
    {
        $data = $request->all();
        if (isset($data['equipe_id'])) $id = $data['equipe_id'];


        $pathImagem = Parametro::where('name','path_imagens')->first()->value;
        $pathImagem = public_path('imagens').'/';
        $logo = $pathImagem.'logo.png';
        $logo = $this->converimaget($logo);

        $dataRelatorio = date('d/m/Y');

        $equipe = $this->equipe->find($id);
        if ($equipe)
            $nomeEquipe = $equipe->nome_equipe;
        else $nomeEquipe = '';

        $listaJogadores = $this->jogador->liberadosByEquipes($id)->get();

        $listaDirigentes = $this->dirigente->byEquipe($id)->liberados()->orderByNome()->get();


        //return view('relatorios.equipes.pdf',compact('listaJogadores'));

        $html = view('relatorios.equipes.pdf',compact('listaJogadores','listaDirigentes','logo',
            'nomeEquipe','dataRelatorio'))->render();
        //return $html;
        return $this->pdf
            ->load($html,'A4')
            ->show();
    }

    public function equipeJogadoresget(Request $request, $eq, $cp, $pt)
    {
        $dados = $request->all();
        if (isset($dados['equipe_id'])) $eq = $dados['equipe_id'];

        $pathImagem = public_path('imagens').'/';
        $logo = $pathImagem.'logo.png';
        $logo = $this->converimaget($logo);

        $competicao = $this->competicao->find($cp);
        $partida    = $this->partida->find($pt);
        $local      = $this->local->find($partida->local_id);

        $equipe = $this->equipe->find($eq);
        if ($equipe)
            $nomeEquipe = $equipe->nome_equipe;
        else $nomeEquipe = '';

        $dataRelatorio = date('d/m/Y');

        $nomeCompeticao = $competicao->nome;
        $dataPartida    = datetime_to_string($partida->inicio);
        $nomePartida    = $local->nome;

        $sumula = $this->sumula->byCompeticaoPartida($cp,$pt)->first();
        if ($sumula) {
            $sumulaEquipe = $this->sumulaEquipes->getDirigenteEquipe($sumula->id, $eq)->first();
            if ($sumulaEquipe) $nomeDirigente = $sumulaEquipe->nome_dirigente;
            else $nomeDirigente = '';
        } else $nomeDirigente = '';

        $listaJogadores = $this->jogador->liberadosByEquipes($eq)->get();

        foreach ($listaJogadores as $key=> $listaJogadore) {
            $docsJogador = DB::table('jogador_documentos')->select('foto_jogador')->where('jogador_id', $listaJogadore->id)->first();
            if ($docsJogador != null) {
                if ($docsJogador->foto_jogador == null || strlen($docsJogador->foto_jogador) == 0) {
                    $fotoJogador = $pathImagem.'No-Image-Person.jpg';
                } else {
                    $fotoJogador = $pathImagem . $docsJogador->foto_jogador;
                }
            }
            if(file_exists($fotoJogador)){
                $listaJogadores[$key]['foto_jogador'] = $this->converimaget($fotoJogador);
            } else {
                $listaJogadores[$key]['foto_jogador'] = $fotoJogador;
            }
        }

        $listaDirigentes = $this->dirigente->byEquipe($eq)->liberados()->orderByNome()->get();

        foreach ($listaDirigentes as $key=> $listaDirigente) {
            $docsDirigente = DB::table('dirigente_documentos')->select('foto_dirigente')->where('dirigente_id', $listaDirigente->id)->first();
            if ($docsDirigente != null) {
                if ($docsDirigente->foto_dirigente == null || strlen($docsDirigente->foto_dirigente) == 0) {
                    $fotoDirigente = $pathImagem.'No-Image-Person.jpg';
                } else {
                    $fotoDirigente = $pathImagem . $docsDirigente->foto_dirigente;
                }
            }
            if(file_exists($fotoDirigente)){
                $listaDirigentes[$key]['foto_dirigente'] = $this->converimaget($fotoDirigente);
            } else {
                $listaDirigentes[$key]['foto_dirigente'] = $fotoDirigente;
            }
        }


        //   dd($listaDirigentes);

        //return view('relatorios.equipes.pdf',compact('listaJogadores'));

        $html = view('relatorios.equipes.pdf',compact('listaJogadores','listaDirigentes','logo',
            'nomeEquipe', 'nomeCompeticao',
            'dataPartida', 'nomePartida', 'dataRelatorio', 'nomeDirigente'))->render();
        return $html;
        //return $this->pdf
        //   ->load($html,'A4')
        //    ->show();
    }


    protected function converimaget($img)
    {
        $path = $img;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;

    }


}
