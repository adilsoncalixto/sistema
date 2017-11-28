<?php

namespace App\Http\Controllers\Relatorios;

use App\Models\Categoria;
use App\Models\Classificacao;
use App\Models\Competicao;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Vsmoraes\Pdf\Pdf;

class ClassificacaoController extends Controller
{

    /**
     * @var Competicao
     */
    private $competicao;
    /**
     * @var Categoria
     */
    private $categoria;
    /**
     * @var Pdf
     */
    private $pdf;
    /**
     * @var Classificacao
     */
    private $classificacao;

    public function __construct(Competicao $competicao, Categoria $categoria, Classificacao $classificacao, Pdf $pdf)
    {

        $this->competicao = $competicao;
        $this->categoria = $categoria;
        $this->pdf = $pdf;
        $this->classificacao = $classificacao;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaCompeticoes = array('-1' => 'Selecione') + $this->competicao->orderBy('nome')->lists('nome', 'id')->toArray();
        $listaCategorias  = array('-1' => 'Selecione') + $this->categoria->orderBy('nome_categoria')->lists('nome_categoria', 'id')->toArray();

        return view('relatorios.classificacao.tela', compact('listaCompeticoes', 'listaCategorias'));
    }

    public function showPdf(Request $request)
    {
        $dados = $request->all();

        $dataRelatorio = date('d/m/Y');

        $pathImagem = public_path('imagens').'/';
        $logo = $pathImagem.'logo.png';
        $logo = $this->converimaget($logo);

        $competicao = $this->competicao->find($dados['competicao_id']);
        if ($competicao)
            $competicao = $competicao->nome;

        $categoria  = $this->categoria->find($dados['categoria_id']);
        if ($categoria)
            $categoria = $categoria->nome_categoria;

        DB::table('classificacoes')->where('id_competicao','=',$dados['competicao_id'])->where('id_categoria','=',$dados['categoria_id'])->delete();

        $lista = DB::select(DB::raw(' SELECT COM.`id` AS id_competicao,
                                                   PAR.`categoria_id` AS id_categoria,
                                                   PAR.ID AS id_partida,
                                                   PAR.`equipe1_id` AS id_equipe1,
                                                   EQU1.`nome_equipe` AS equipe1,
                                                   (SELECT COUNT(*) FROM `historico_partidas` HP  WHERE HP.`id_competicao` = COM.`id` AND HP.`id_equipe` = PAR.`equipe1_id`) AS gols1,
                                                   (SELECT COUNT(*) FROM `historico_partidas` HP  WHERE HP.`id_competicao` = COM.`id` AND HP.`id_equipe` = PAR.`equipe2_id`) AS gols_sofridos1,
                                                   PAR.`equipe2_id` AS id_equipe2,
                                                   EQU2.`nome_equipe` AS equipe2,
                                                   (SELECT COUNT(*) FROM `historico_partidas` HP  WHERE HP.`id_competicao` = COM.`id` AND HP.`id_equipe` = PAR.`equipe2_id`) AS gols2,
                                                   (SELECT COUNT(*) FROM `historico_partidas` HP  WHERE HP.`id_competicao` = COM.`id` AND HP.`id_equipe` = PAR.`equipe1_id`) AS gols_sofridos2
                                                  FROM `competicoes` COM 
                                                  JOIN `partidas` PAR ON PAR.`competicao_id` = COM.`id`
                                                  JOIN `equipes` EQU1 ON `EQU1`.`id` = PAR.`equipe1_id`
                                                  JOIN `equipes` EQU2 ON `EQU2`.`id` = PAR.`equipe2_id`
                                                  WHERE COM.`id` = '.$dados['competicao_id'].
                                                    ' AND PAR.`categoria_id` = '.$dados['categoria_id'].' ORDER BY PAR.`id`'));

        foreach ($lista as $row) {

            $pontos1 = 0;
            $pontos2 = 0;
            $vitoria1 = 0;
            $vitoria2 = 0;
            $derrota1 = 0;
            $derrota2 = 0;
            $empate1 = 0;
            $empate2 = 0;

            if ($row->gols1 == $row->gols2) {
                $pontos1 = 1;
                $pontos2 = 1;
                $empate1 = 1;
                $empate2 = 1;
            } else if ($row->gols1 > $row->gols2) {
                $pontos1 = 3;
                $vitoria1 = 1;
            } else if ($row->gols1 < $row->gols2) {
                $pontos2 = 3;
                $vitoria2 = 1;
            }

            $registro = [
                'id_competicao'=> $row->id_competicao,
                'id_categoria'=> $row->id_categoria,
                'id_partida'=> $row->id_partida,
                'id_equipe1'=> $row->id_equipe1,
                'equipe1'=> $row->equipe1,
                'gols1'=> $row->gols1,
                'gols_sofridos1' => $row->gols_sofridos1,
                'vitorias1' => $vitoria1,
                'derrotas1' => $derrota1,
                'empates1' => $empate1,
                'pontos1'=> $pontos1,
                'id_equipe2'=> $row->id_equipe2,
                'equipe2'=> $row->equipe2,
                'gols2'=> $row->gols2,
                'gols_sofridos2' => $row->gols_sofridos2,
                'vitorias2' => $vitoria2,
                'derrotas2' => $derrota2,
                'empates2' => $empate2,
                'pontos2' => $pontos2
            ];

            DB::table('classificacoes')->insert($registro);

        }


       // DB::select('call proc_classificacao(?,?)',array($dados['competicao_id'],$dados['categoria_id']));

        $listaClassificacao = $this->classificacao->listaClassificacao($dados['competicao_id'],$dados['categoria_id'])->get();

        $html = view('relatorios.classificacao.relatorio',
            compact('listaClassificacao', 'dataRelatorio', 'logo', 'competicao', 'categoria'));

        return $this->pdf->load($html,'A4')->show();
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
