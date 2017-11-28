<?php

namespace App\Http\Controllers\Relatorios;

use App\Models\Categoria;
use App\Models\Competicao;
use App\Models\HistoricoPartida;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use File;
use Vsmoraes\Pdf\Pdf;

class ArtilhariaController extends Controller
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
     * @var HistoricoPartida
     */
    private $historicoPartida;

    public function __construct(Competicao $competicao, Categoria $categoria,
                                HistoricoPartida $historicoPartida, Pdf $pdf)
    {

        $this->competicao = $competicao;
        $this->categoria = $categoria;
        $this->pdf = $pdf;
        $this->historicoPartida = $historicoPartida;
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

        return view('relatorios.artilharia.tela', compact('listaCompeticoes', 'listaCategorias'));
    }


    public function showTela(Request $request)
    {
        $dados = $request->all();
        $pathImagem = public_path('imagens').'/';
        $logo = $pathImagem.'logo.png';
        $logo = $this->converimaget($logo);

        $listaArtilharia = $this->historicoPartida->artilharia($dados['competicao_id'],$dados['categoria_id']);
        $dataRelatorio = date('d/m/Y');

        foreach ($listaArtilharia as $row) {
            if ($row['foto_jogador']) {
                if ($row['foto_jogador'] != '') {
                    $foto = $this->converimaget($pathImagem.$row['foto_jogador']);
                } else $foto = null;
            } else $foto = null;
            $row['foto_jogador'] = $foto;
        }

        return view('relatorios.artilharia.relatorio', compact('listaArtilharia', 'dataRelatorio', 'logo'));
    }

    public function showPdf(Request $request)
    {
        $dados = $request->all();
        $pathImagem = public_path('imagens').'/';
        $logo = $pathImagem.'logo.png';
        $logo = $this->converimaget($logo);

        $listaArtilharia = $this->historicoPartida->artilharia($dados['competicao_id'],$dados['categoria_id']);
        $dataRelatorio = date('d/m/Y');
        $competicao = $this->competicao->find($dados['competicao_id']);
        if ($competicao)
            $competicao = $competicao->nome;

        $categoria  = $this->categoria->find($dados['categoria_id']);
        if ($categoria)
            $categoria = $categoria->nome_categoria;




        foreach ($listaArtilharia as $row) {

            if ($row->foto_jogador) {
                if ($row->foto_jogador != '') {
                    $foto = $pathImagem.$row->foto_jogador;
                    $foto = File::exists($foto) ? $this->converimaget($foto) : 'No-Image-Person.jpg';
                } else $foto = 'No-Image-Person.jpg';
            } else $foto = 'No-Image-Person.jpg';
            if ($foto == 'No-Image-Person.jpg') {
                $row->foto_jogador = $this->converimaget($pathImagem . $foto);

            }
        }

        $html = view('relatorios.artilharia.relatorio',
            compact('listaArtilharia', 'dataRelatorio', 'logo', 'competicao', 'categoria'));
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
