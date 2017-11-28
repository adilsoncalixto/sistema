<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Parametro;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;

use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Mockery\Exception;

use App\Models\Jogador;
use App\Models\Jogador_documento;
use App\Models\Equipe;
use Vsmoraes\Pdf\Pdf;

class JogadorController extends Controller
{
    protected $user;
    protected $idEquipe;
    /**
     * @var Pdf
     */
    private $pdf;
    /**
     * @var Categoria
     */
    private $categoria;

    public function __construct(Categoria $categoria, Pdf $pdf)
    {
        $this->user = Auth::User();
        if ($this->user->is('equipe')) {

            $eqp = Equipe::where('email_equipe', '=', $this->user->email)->first();


            $this->idEquipe = $eqp->id;
        } else {
            $this->idEquipe = null;
        }
        $this->pdf = $pdf;
        $this->categoria = $categoria;
    }

    public function index() {

        if ($this->user->is('equipe')) {
            $jogadores = Jogador::select('jogadores.id','nome_jogador','nome_equipe','nome_categoria','numero_registro','cpf_jogador',
                'rg_jogador','email_jogador','liberado')
                ->where('jogadores.equipe_id','=',$this->idEquipe)
                ->leftjoin('equipes','jogadores.equipe_id', '=', 'equipes.id')
                ->leftjoin('categorias','jogadores.categoria_id', '=', 'categorias.id')
                ->get();

        } else if ($this->user->is('admin')) {
            $jogadores = Jogador::select('jogadores.id','nome_jogador','nome_categoria','nome_equipe','numero_registro','cpf_jogador',
                'rg_jogador','email_jogador', 'liberado')
                ->leftjoin('equipes','jogadores.equipe_id', '=', 'equipes.id')
                ->leftjoin('categorias','jogadores.categoria_id', '=', 'categorias.id')
                ->get();

        }  else $jogadores = null;


        return view('jogadores.jogadores-lista',compact('jogadores'));
    }

    public function cadastro(Request $request,$idRegistro = -1) {

        if ($this->user->is('admin')) {
            $equipes = array('-1' => 'Selecione') + Equipe::orderBy('nome_equipe')->lists('nome_equipe','id')->toArray();
            $equipe = -1;
            $categorias = array('-1' => 'Selecione');
            $categoria = -1;
        } else if ($this->user->is('equipe')) {
            $equipes = array('-1' => 'Selecione') + Equipe::where('id','=',$this->idEquipe)->orderBy('nome_equipe')->lists('nome_equipe','id')->toArray();
            $equipe = $this->idEquipe;
            $categorias = array('-1' => 'Selecione');
            $categoria = -1;
        }

        $categorias = $categorias + $this->categoria->orderBy('nome_categoria')->lists('nome_categoria','id')->toArray();


        if ($request->method() == 'GET') {

            $fotoCPF = '';
            $fotoRG = '';
            $fotoCR = '';
            $txtDisabled = 'disabled';
            $disableFrmFoto          = $txtDisabled;
            $disableFrmCpf           = $txtDisabled;
            $disableFrmRg            = $txtDisabled;
            $disableFrmCr            = $txtDisabled;
            $disableFotoVisualizacao = $txtDisabled;
            $disableCPFVisualizacao  = $txtDisabled;
            $disableCPFRemocao       = $txtDisabled;
            $disableRGVisualizacao   = $txtDisabled;
            $disableFotoRemocao      = $txtDisabled;
            $disableRGRemocao        = $txtDisabled;
            $disableCRVisualizacao   = $txtDisabled;
            $disableCRRemocao        = $txtDisabled;
            $fotoJogador = '/imagens/No-Image-Person.jpg';

            $idRegistro = intval($idRegistro);
            $data_nascimento = null;
            $Date = date('Y-m-d');// "2013-06-21";
            $data_validade =  date('d/m/Y', strtotime($Date. ' + 2 years'));

            if ($idRegistro > 0) {
                $jogador = DB::table('jogadores')->where('id', $idRegistro)->first();

                if ($jogador != null) {
                    $equipe = $jogador->equipe_id;
                    $categoria = $jogador->categoria_id;
                    $disableFrmFoto = '';
                    $disableFrmCpf  = '';
                    $disableFrmRg   = '';
                    $disableFrmCr   = '';
                    //dd($jogador);
                    if ($jogador->data_nascimento == '"0000-00-00"') {
                        $jogador->data_nascimento = null;
                    }
                    if ($jogador->data_validade == '0000-00-00') {
                        $jogador->data_validade = null;
                    }


                    $data_nascimento = date_format(date_create($jogador->data_nascimento),"d/m/Y");
                    $data_validade   = date_format(date_create($jogador->data_validade),"d/m/Y");
                   //  dd($data_nascimento);
                    $docsJogador = DB::table('jogador_documentos')->where('jogador_id', $idRegistro)->first();
                    if ($docsJogador != null) {
                        if ($docsJogador->foto_jogador == null || strlen($docsJogador->foto_jogador) == 0) {
                            $fotoJogador = '/imagens/No-Image-Person.jpg';
                        } else {
                            $fotoJogador = '/imagens/'.$docsJogador->foto_jogador;
                            $disableFotoVisualizacao = '';
                            $disableFotoRemocao      = '';
                            //$fotoJogador = '';
                        }

                        if ($docsJogador->documento_cpf != null && strlen($docsJogador->documento_cpf) > 0) {
                            $disableCPFVisualizacao = '';
                            $disableCPFRemocao      = '';
                            $fotoCPF = $docsJogador->documento_cpf;
                        } else {
                            $fotoCPF = 'Selecionar arquivo';
                        }
                        if ($docsJogador->documento_rg != null && strlen($docsJogador->documento_rg) > 0) {
                            $disableRGVisualizacao = '';
                            $disableRGRemocao      = '';
                            $fotoRG = $docsJogador->documento_rg;
                        } else {
                            $fotoRG = '';
                        }
                        if ($docsJogador->documento_cr != null && strlen($docsJogador->documento_cr) > 0) {
                            $disableCRVisualizacao = '';
                            $disableCRRemocao      = '';
                            $fotoCR = $docsJogador->documento_cr;
                        } else {
                            $fotoCR = '';
                        }
                    }

                }
            } else {
                $txtDisabled = 'disabled';
                $disableFrmFoto          = $txtDisabled;
                $disableFrmCpf           = $txtDisabled;
                $disableFrmRg            = $txtDisabled;
                $disableFrmCr            = $txtDisabled;
                $disableFotoVisualizacao = $txtDisabled;
                $disableCPFVisualizacao  = $txtDisabled;
                $disableCPFRemocao       = $txtDisabled;
                $disableRGVisualizacao   = $txtDisabled;
                $disableFotoRemocao      = $txtDisabled;
                $disableRGRemocao        = $txtDisabled;
                $disableCRVisualizacao   = $txtDisabled;
                $disableCRRemocao        = $txtDisabled;
                $jogador = null;
                $docsJogador['documento_cpf'] = '';
                $docsJogador['documento_cpf'] = '';
                $docsJogador['documento_cr'] = '';
                $fotoJogador = '/imagens/No-Image-Person.jpg';
            }


	   // dd($equipe);
           // $equipe=1;
            $idequipe = $equipe;

            if (Auth::User()->is('equipe')) {

                if ( $jogador != null && ($jogador->analizando == 1 || $jogador->liberado == 1)) {
                    $disableFrmFoto = 'disabled';
                    $disableFotoRemocao = $disableFrmFoto;
                    $disableFrmCpf = $disableFrmFoto;
                    $disableCPFRemocao = $disableFrmFoto;
                    $disableFrmRg = $disableFrmFoto;
                    $disableRGRemocao = $disableFrmFoto;
                    $disableFrmCr = $disableFrmFoto;
                    $disableCRRemocao = $disableFrmFoto;
                }
            }


            return view('jogadores.jogadores-cadastro', compact('equipes','idequipe','jogador','fotoJogador',
            'disableFrmFoto','disableFrmCpf','disableFrmRg','disableFrmCr','docsJogador',
                'disableFotoVisualizacao','disableFotoRemocao', 'fotoCPF', 'fotoRG', 'fotoCR',
                'disableCPFVisualizacao','disableCPFRemocao','data_nascimento', 'data_validade',
                'disableRGVisualizacao','disableRGRemocao','categorias','categoria',
                'disableCRVisualizacao','disableCRRemocao'
            ));
        }
        elseif ($request->method() == 'POST') {

            $id  = $request->input('id');

            try {

                if (strlen($id) > 0) {
                    $tblJogador = Jogador::where('id',$id)->first();

                    $tblJogador->nome_jogador     = $request->input('nomecompleto');
                    $tblJogador->equipe_id        = $request->input('equipe');
                    $tblJogador->categoria_id     = $request->input('categoria');

                    if ($tblJogador->cpf_jogador != $request->input('cpf')) {
                        $tblJogador->cpf_jogador      = $request->input('cpf');
                    }
                    $tblJogador->rg_jogador       = $request->input('rg');
                    $tblJogador->numero_registro  = $request->input('registro');
                    $tblJogador->telefone_jogador = $request->input('telefone');
                    $tblJogador->celular_jogador  = $request->input('celular');
                    $tblJogador->email_jogador    = $request->input('email');
                    $tblJogador->data_nascimento  = implode("-",array_reverse(explode("/",$request->input('nascimento'))));
                    $tblJogador->data_validade    = implode("-",array_reverse(explode("/",$request->input('validade'))));
                    $tblJogador->endereco_jogador   = $request->input('endereco');
                    $tblJogador->analizando         = ($request->input('analizando') == null) ? 0 : 1;
                    $tblJogador->liberado           = ($request->input('liberado') == null) ? 0 : 1;
                    $tblJogador->save();
                   // dd($tblJogador, $request->all());

                    return redirect('./jogador/cadastro/'.$id);

                } else {
                    $tblJogador = new Jogador;
                    $tblJogador->nome_jogador     = $request->input('nomecompleto');
                    $tblJogador->equipe_id        = $request->input('equipe');
                    $tblJogador->categoria_id     = $request->input('categoria');
                    $tblJogador->cpf_jogador      = $request->input('cpf');
                    $tblJogador->rg_jogador       = $request->input('rg');
                    $tblJogador->numero_registro  = $request->input('registro');
                    $tblJogador->telefone_jogador = $request->input('telefone');
                    $tblJogador->celular_jogador  = $request->input('celular');
                    $tblJogador->email_jogador    = $request->input('email');
                    $tblJogador->data_nascimento  = implode("-",array_reverse(explode("/",$request->input('nascimento'))));
                    $tblJogador->data_validade     =implode("-",array_reverse(explode("/",$request->input('validade'))));
                    $tblJogador->endereco_jogador   = $request->input('endereco');
                    $tblJogador->analizando         = ($request->input('analizando') == null) ? 0 : 1;
                    $tblJogador->liberado           = ($request->input('liberado') == null) ? 0 : 1;
                    $tblJogador->save();
                    $idRegistro = $tblJogador->id;


                    //$jogador = DB::table('jogadores')->where('id', $idRegistro)->first();

                    return redirect('./jogador/cadastro/'.$idRegistro);
                }

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A gravação do registro foi realizada com sucesso';
                $botoes = '<a href="/jogador/cadastro" class="btn btn-outline btn-primary">Novo</a>';
                $botoes = $botoes.' <a href="/jogador/lista" class="btn btn-outline btn-primary">Lista</a>';

            } catch (\Exception $e) {
                $corPainel = 'panel-red';
                $txtTitulo = 'Erro';
                if ((strpos($e->getMessage(), 'for key \'jogadores_cpf_jogador_unique\'') > 0) ||
                (strpos($e->getMessage(), 'Integrity constraint violation: 1062 Duplicate entry') > 0))
                {
                    $txtMensagem = 'Já existem um jogador cadastrado com este cpf'.$e->getMessage();
                } else {
                    $txtMensagem = 'Ocorreu um erro na gravação do registro '. $e->getMessage();
                }

                $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            }

            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));

        }
    }

    public function excluiget($idRegistro) {

        try {
            DB::table('jogadores')
                ->where('id', $idRegistro)
                ->delete();

            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
            $botoes = '<a href="/jogador/categoria" class="btn btn-outline btn-primary">Nova</a>';
            $botoes = $botoes.' <a href="/jogador/lista" class="btn btn-outline btn-primary">Lista</a>';

        } catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na exclusão do registro '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    public function exclui(Request $request)
    {
        $data = $request->input();
        $idRegistro = $data['idexclui'];
        try {
            $doc = Jogador_documento::where('jogador_id','=', $idRegistro)->first();
            if ($doc) $doc->delete();
            $jog = Jogador::find($idRegistro);
            if ($jog) $jog->delete();
            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
            $botoes = '';//<a href="/jogador/categoria" class="btn btn-outline btn-primary">Nova</a>';
            $botoes = $botoes.' <a href="/jogador/lista" class="btn btn-outline btn-primary">Lista</a>';
            //    return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));


        }  catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na exclusão do registro '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    public function gravaDocumentoFoto(Request $request) {

        try {
            $idRegistro = intval($request->input('id'));
        } catch (Exception $e) {
            $idRegistro = -1;
        }

        if($request->file('foto_jogador')) {
            $img = $request->file('foto_jogador'); //recebe a imagem
            $ext = pathinfo($img->getClientOriginalName(), PATHINFO_EXTENSION) ?: '';
            $img_nome =  str_pad($idRegistro, 5, "0", STR_PAD_LEFT).'_foto.'.$ext;  //nome da imagem
            $img->move(public_path().'/imagens', $img_nome); //move a imagem para pasta

            $jogador = Jogador_documento::where('jogador_id','=', $idRegistro)->first();

            if ($jogador == null) {
                $jogador = new Jogador_documento;
            }
            $jogador->jogador_id   = $idRegistro;
            $jogador->foto_jogador = $img_nome;
            $jogador->save();

            return redirect('/jogador/cadastro/'.$idRegistro);

        } else {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Não foi fornecida uma imagem ';
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }
        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    public function gravaDocumentoCPF(Request $request) {

        try {
            $idRegistro = intval($request->input('id'));
        } catch (Exception $e) {
            $idRegistro = -1;
        }

        if($request->file('documento_cpf')) {
            $img = $request->file('documento_cpf'); //recebe a imagem
            $ext = pathinfo($img->getClientOriginalName(), PATHINFO_EXTENSION) ?: '';
            $img_nome =  str_pad($idRegistro, 5, "0", STR_PAD_LEFT).'_cpf.'.$ext;  //nome da imagem
            $img->move(public_path().'/imagens', $img_nome); //move a imagem para pasta

            $jogador = Jogador_documento::where('jogador_id','=', $idRegistro)->first();

            if ($jogador == null) {
                $jogador = new Jogador_documento;
            }
            $jogador->jogador_id    = $idRegistro;
            $jogador->documento_cpf = $img_nome;
            $jogador->save();

            return redirect('/jogador/cadastro/'.$idRegistro);

        } else {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Não foi fornecida uma imagem ';
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    public function gravaDocumentoRG(Request $request) {

        try {
            $idRegistro = intval($request->input('id'));
        } catch (Exception $e) {
            $idRegistro = -1;
        }

        if($request->file('documento_rg')) {
            $img = $request->file('documento_rg'); //recebe a imagem
            $ext = pathinfo($img->getClientOriginalName(), PATHINFO_EXTENSION) ?: '';
            $img_nome =  str_pad($idRegistro, 5, "0", STR_PAD_LEFT).'rg.'.$ext;  //nome da imagem
            $img->move(public_path().'/imagens', $img_nome); //move a imagem para pasta

            $jogador = Jogador_documento::where('jogador_id','=', $idRegistro)->first();

            if ($jogador == null) {
                $jogador = new Jogador_documento;
            }
            $jogador->jogador_id    = $idRegistro;
            $jogador->documento_rg  = $img_nome;
            $jogador->save();

            return redirect('/jogador/cadastro/'.$idRegistro);

        } else {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Não foi fornecida uma imagem ';
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    public function gravaDocumentoCR(Request $request) {

        try {
            $idRegistro = intval($request->input('id'));
        } catch (Exception $e) {
            $idRegistro = -1;
        }

        if($request->file('documento_cr')) {
            $img = $request->file('documento_cr'); //recebe a imagem
            $ext = pathinfo($img->getClientOriginalName(), PATHINFO_EXTENSION) ?: '';
            $img_nome =  str_pad($idRegistro, 5, "0", STR_PAD_LEFT).'cr.'.$ext;  //nome da imagem
            $img->move(public_path().'/imagens', $img_nome); //move a imagem para pasta

            $jogador = Jogador_documento::where('jogador_id','=', $idRegistro)->first();

            if ($jogador == null) {
                $jogador = new Jogador_documento;
            }
            $jogador->jogador_id    = $idRegistro;
            $jogador->documento_cr  = $img_nome;
            $jogador->save();

            return redirect('/jogador/cadastro/'.$idRegistro);

        } else {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Não foi fornecida uma imagem ';
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    public function removeDocumentoFoto($idJogador) {

        try {
            $docJogador = Jogador_documento::where('jogador_id','=', $idJogador)->first();
            $arquivo = public_path().'/imagens/'.$docJogador->foto_jogador;
            unlink($arquivo);
            $docJogador->foto_jogador = null;
            $docJogador->save();
            return redirect('/jogador/cadastro/'.$idJogador);
        } catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na remoção da foto '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        }
    }

    public function removeDocumentoCpf($idJogador) {

        try {
            $docJogador = Jogador_documento::where('jogador_id','=', $idJogador)->first();
            $docJogador->documento_cpf = null;
            $docJogador->save();
            return redirect('/jogador/cadastro/'.$idJogador);
        } catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na remoção da imagem do CPF '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        }
    }

    public function removeDocumentoRg($idJogador) {

        try {
            $docJogador = Jogador_documento::where('jogador_id','=', $idJogador)->first();
            $docJogador->documento_rg = null;
            $docJogador->save();
            return redirect('/jogador/cadastro/'.$idJogador);
        } catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na remoção da imagem do RG '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        }
    }

    public function removeDocumentoCr($idJogador) {

        try {
            $docJogador = Jogador_documento::where('jogador_id','=', $idJogador)->first();
            $docJogador->documento_cr = null;
            $docJogador->save();
            return redirect('/jogador/cadastro/'.$idJogador);
        } catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na remoção da imagem '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        }
    }

    public function carteirinha($id=0)
    {
        $bgball = public_path('imagens').'/'.'verso1bg.jpg';
        $bgball = $this->converimaget($bgball);

        $equipe = null;
        $registro = null;
        $jogador = Jogador::find($id);
        $pathImagem = Parametro::where('name','path_imagens')->first()->value;
        if (!$pathImagem)
            $pathImagem = public_path('imagens').'/';
        if ($jogador) {
            $equipe = Equipe::find($jogador->equipe_id);
            $equipe = $equipe->nome_equipe;
            //$logo = asset('/imagens/logo.png');
            $logo = $pathImagem.'logo.png';
            $logo = $this->converimaget($logo);
            $registro = str_pad(strval($jogador->id),5,'0',STR_PAD_LEFT) ;
            //$validade = date_format($jogador->data_validade,'d/m/Y') ;
            $validade = date_format(date_create($jogador->data_validade),"d/m/Y");

            $documento = Jogador_documento::where('jogador_id','=',$jogador->id)->first();
            if ($documento == null || $documento->foto_jogador == '')  {
                $foto = $pathImagem.'No-Image-Person.jpg';
                $foto = $this->converimaget($foto);
            } else {
                $foto = $pathImagem.$documento->foto_jogador;
                $foto = $this->converimaget($foto);

            }

            $html = view('jogadores.jogador-carteirinha',compact('jogador','bgball','foto','logo','equipe', 'registro', 'validade'))->render();
          //  return $html;
            return $this->pdf
                ->load($html,'A3','landscape')
                ->show();
        }

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
