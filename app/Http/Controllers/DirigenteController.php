<?php

namespace App\Http\Controllers;

use App\Models\Dirigente;
use App\Models\DirigenteDocumento;
use App\Models\DirigenteTipo;
use App\Models\Equipe;
use App\Models\Parametro;
use Illuminate\Http\Request;
use App\Http\Requests;

use DB;
use Illuminate\Support\Facades\Auth;

use Vsmoraes\Pdf\Pdf;

class DirigenteController extends Controller
{
    /**
     * @var Dirigente
     */
    private $dirigente;
    /**
     * @var Equipe
     */
    private $equipe;
    /**
     * @var DirigenteDocumento
     */
    private $dirigenteDocumento;
    /**
     * @var Pdf
     */
    private $pdf;
    /**
     * @var DirigenteTipo
     */
    private $dirigenteTipo;

    public function __construct(
        Dirigente $dirigente,
        DirigenteTipo $dirigenteTipo,
        Equipe $equipe,
        DirigenteDocumento $dirigenteDocumento,
        Pdf $pdf)
    {

        $this->dirigente = $dirigente;
        $this->equipe = $equipe;

        $this->user = Auth::User();
        if ($this->user->is('equipe')) {
            $eqp = Equipe::where('email_equipe', '=', $this->user->email)->first();
            $this->idEquipe = $eqp->id;
        } else {
            $this->idEquipe = null;
        }
        $this->dirigenteDocumento = $dirigenteDocumento;
        $this->pdf = $pdf;
        $this->dirigenteTipo = $dirigenteTipo;
    }

    public function index() {

        if ($this->user->is('equipe')) {
            $dirigentes = $this->dirigente->select('dirigentes.id','nome_dirigente','nome_equipe','cpf_dirigente','telefone_dirigente','celular_dirigente',
                'cargo','rg_dirigente','email_dirigente','liberado')->leftjoin('equipes','dirigentes.equipe_id', '=', 'equipes.id')->where('dirigentes.equipe_id','=',$this->idEquipe)->get();
        } else {
            $dirigentes = $this->dirigente->select('dirigentes.id','nome_dirigente','nome_equipe','cpf_dirigente','telefone_dirigente','celular_dirigente',
                'cargo','rg_dirigente','email_dirigente','liberado')->leftjoin('equipes','dirigentes.equipe_id', '=', 'equipes.id')->get();
        }



        return view('dirigentes.dirigentes-lista',compact('dirigentes'));
    }


    public function cadastro(Request $request, $idRegistro=-1)
    {
        if ($this->user->is('admin')) {
            $equipes = array('-1' => 'Selecione') + Equipe::lists('nome_equipe','id')->toArray();
            $idequipe = -1;
        } else if ($this->user->is('equipe')) {
            $equipes = array('-1' => 'Selecione') + Equipe::where('id','=',$this->idEquipe)->lists('nome_equipe','id')->toArray();
            $idequipe = $this->idEquipe;
        }

        $tipos = array('-1' => 'Selecione') + $this->dirigenteTipo->lists('nome','id')->toArray();
        $idtipo = null;

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
            $fotoDirigente = '/imagens/No-Image-Person.jpg';

            $docsDirigente['documento_cpf'] = '';
            $docsDirigente['documento_cpf'] = '';
            $docsDirigente['documento_cr'] = '';
            $fotoDirigente = '/imagens/No-Image-Person.jpg';

            $idRegistro = intval($idRegistro);
            $data_nascimento = null;
            $Date = date('Y-m-d');// "2013-06-21";
            $data_validade =  date('d-m-Y', strtotime($Date. ' + 2 years'));


            if ($idRegistro > 0) {
                $dirigente = $this->dirigente->find($idRegistro);
               // dd($dirigente);

                if ($dirigente != null) {
                    $idequipe = $dirigente->equipe_id;
                    $idtipo = $dirigente->dirigente_tipo_id;
                    $disableFrmFoto = '';
                    $disableFrmCpf  = '';
                    $disableFrmRg   = '';
                    $disableFrmCr   = '';
                    //dd($jogador);
                    if ($dirigente->data_nascimento == '"0000-00-00"') {
                        $dirigente->data_nascimento = null;
                    }
                    if ($dirigente->data_validade == '0000-00-00') {
                        $dirigente->data_validade = null;
                    }


                    $data_nascimento = date_format(date_create($dirigente->data_nascimento),"d/m/Y");
                    $data_validade   = date_format(date_create($dirigente->data_validade),"d/m/Y");
                    //  dd($data_nascimento);
                    $docsDirigente = DB::table('dirigente_documentos')->where('dirigente_id', $idRegistro)->first();
                    if ($docsDirigente != null) {
                        if ($docsDirigente->foto_dirigente == null || strlen($docsDirigente->foto_dirigente) == 0) {
                            $fotoDirigente = '/imagens/No-Image-Person.jpg';
                        } else {
                            $fotoDirigente = '/imagens/'.$docsDirigente->foto_dirigente;
                            $disableFotoVisualizacao = '';
                            $disableFotoRemocao      = '';
                            //$fotoJogador = '';
                        }

                        if ($docsDirigente->documento_cpf != null && strlen($docsDirigente->documento_cpf) > 0) {
                            $disableCPFVisualizacao = '';
                            $disableCPFRemocao      = '';
                            $fotoCPF = $docsDirigente->documento_cpf;
                        } else {
                            $fotoCPF = 'Selecionar arquivo';
                        }
                        if ($docsDirigente->documento_rg != null && strlen($docsDirigente->documento_rg) > 0) {
                            $disableRGVisualizacao = '';
                            $disableRGRemocao      = '';
                            $fotoRG = $docsDirigente->documento_rg;
                        } else {
                            $fotoRG = '';
                        }
                        if ($docsDirigente->documento_cr != null && strlen($docsDirigente->documento_cr) > 0) {
                            $disableCRVisualizacao = '';
                            $disableCRRemocao      = '';
                            $fotoCR = $docsDirigente->documento_cr;
                        } else {
                            $fotoCR = '';
                        }
                    }

                } else {
                    $idtipo = null;
                }


            } else {
                $dirigente = null;
            }



            return view('dirigentes.dirigentes-cadastro', compact('equipes','idequipe','dirigente','fotoDirigente',
                'disableFrmFoto','disableFrmCpf','disableFrmRg','disableFrmCr','docsDirigente',
                'disableFotoVisualizacao','disableFotoRemocao', 'fotoCPF', 'fotoRG', 'fotoCR',
                'disableCPFVisualizacao','disableCPFRemocao','data_nascimento', 'data_validade',
                'disableRGVisualizacao','disableRGRemocao','tipos','idtipo',
                'disableCRVisualizacao','disableCRRemocao'
            ));
        }
        elseif ($request->method() == 'POST') {

            $update = 1;
            $dados = $request->all();
            if(array_key_exists('registro', $dados)) unset($dados['registro']);
            if(array_key_exists('_token', $dados)) unset($dados['_token']);
            if(array_key_exists('id', $dados))
                if ($dados['id'] == ''){
                    unset($dados['id']);
                    $update = 0;
                }
            $dados['data_nascimento'] = implode("-",array_reverse(explode("/",$request->input('data_nascimento'))));
            $dados['data_validade']   = implode("-",array_reverse(explode("/",$request->input('data_validade'))));
            if(array_key_exists('analizando', $dados))
                $dados['analizando'] = '1';
            else
                $dados['analizando'] = '0';
            if(array_key_exists('liberado', $dados))
                $dados['liberado'] = '1';
            else
                $dados['liberado'] = '0';

            if ($update) {
                try {
                    $registro = $this->dirigente->find($dados['id']);
                    $registro->update($dados);


                    $corPainel = 'panel-green';
                    $txtTitulo = 'Sucesso';
                    $txtMensagem = 'A gravação do registro foi realizada com sucesso';
                    $botoes = '<a href="/dirigente/cadastro" class="btn btn-outline btn-primary">Novo</a>';
                    $botoes = $botoes.' <a href="/dirigente/lista" class="btn btn-outline btn-primary">Lista</a>';
                }
                catch (\Exception $e) {
                    $corPainel = 'panel-red';
                    $txtTitulo = 'Erro';
                    $txtMensagem = 'Ocorreu um erro na gravação do registro '. $e->getMessage();
                    $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
                }

            }
            else {
                try {
                    $registro = $this->dirigente->insert($dados);

                    $corPainel = 'panel-green';
                    $txtTitulo = 'Sucesso';
                    $txtMensagem = 'A gravação do registro foi realizada com sucesso';
                    $botoes = '<a href="/dirigente/cadastro" class="btn btn-outline btn-primary">Novo</a>';
                    $botoes = $botoes.' <a href="/dirigente/lista" class="btn btn-outline btn-primary">Lista</a>';
                }
                catch (\Exception $e) {
                    $corPainel = 'panel-red';
                    $txtTitulo = 'Erro';
                    $txtMensagem = 'Ocorreu um erro na gravação do registro '. $e->getMessage();
                    $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
                }
            }
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));

        }
    }

    public function exclui(Request $request) {

        try {

            $data = $request->all();
            $idRegistro = $data['idexclui'];

            $documento = $this->dirigenteDocumento->where('dirigente_id','=', $idRegistro)->first();
            if ($documento){
                $pathImagem = Parametro::where('name','path_imagens')->first()->value;
                $pathImagem = public_path('imagens');
                try {
                    $doc = $pathImagem.$documento->foto_dirigente;
                    if (!$documento->foto_dirigente && file_exists($doc)) unlink($doc);
                    $doc = $pathImagem.$documento->documento_cpf;
                    if (!$documento->documento_cpf && file_exists($doc)) unlink($doc);
                    $doc = $pathImagem.$documento->documento_rg;
                    if (!$documento->documento_rg && file_exists($doc)) unlink($doc);
                    $doc = $pathImagem.$documento->documento_cr;
                    if (!$documento->documento_cr && file_exists($doc)) unlink($doc);
                } catch (\Exception $e) {
                    //
                }

                $documento->delete();
            }

            $registro = $this->dirigente->find($idRegistro);
            if ($registro) {

                $registro->delete();

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
                $botoes = '<a href="/dirigente/cadastro" class="btn btn-outline btn-primary">Novo</a>';
                $botoes = $botoes.' <a href="/dirigente/lista" class="btn btn-outline btn-primary">Lista</a>';
            }

        } catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            if (strpos($e->getMessage(), 'Cannot delete or update a parent row: a foreign key constraint fails (`liga_hortolandia`.`dirigente_documentos`, ') > 0)
                $txtMensagem = 'Existe documentos cadastrados para este dirigente';
            else $txtMensagem = 'Ocorreu um erro na exclusão do registro '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    public function gravaDocumentoFoto(Request $request)
    {
        $dados = $request->all();


        if (array_key_exists('foto_dirigente',$dados)) {
            $img = $request->file('foto_dirigente'); //recebe a imagem
            $ext = pathinfo($img->getClientOriginalName(), PATHINFO_EXTENSION) ?: '';
            $img_nome = 'D-'.str_pad($dados['id'], 5, "0", STR_PAD_LEFT).'_foto.'.$ext;  //nome da imagem
            $img->move(public_path().'/imagens', $img_nome); //move a imagem para pasta

            $documento = $this->dirigenteDocumento->where('dirigente_id',$dados['id'])->first();
            if ($documento == null){
                $this->dirigenteDocumento->insert([
                    'dirigente_id' => $dados['id'],
                    'foto_dirigente' => $img_nome
                ]);
            }
            else {
                $documento->update([
                    'foto_dirigente' => $img_nome
                ]);
            }

        }  else {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Não foi fornecida uma imagem ';
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        }

        return redirect('/dirigente/cadastro/'.$dados['id']);
    }

    public function gravaDocumentoCPF(Request $request)
    {
        $dados = $request->all();

        if (array_key_exists('documento_cpf',$dados)) {
            $img = $request->file('documento_cpf'); //recebe a imagem
            $ext = pathinfo($img->getClientOriginalName(), PATHINFO_EXTENSION) ?: '';
            $img_nome = 'D-'.str_pad($dados['id'], 5, "0", STR_PAD_LEFT).'_cpf.'.$ext;  //nome da imagem
            $img->move(public_path().'/imagens', $img_nome); //move a imagem para pasta

            $documento = $this->dirigenteDocumento->where('dirigente_id',$dados['id'])->first();

            if ($documento == null){
                $this->dirigenteDocumento->insert([
                    'dirigente_id' => $dados['id'],
                    'documento_cpf' => $img_nome
                ]);
            }
            else {
                $documento->update([
                    'documento_cpf' => $img_nome
                ]);
            }

        } else {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Não foi fornecida uma imagem ';
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        }
        return redirect('/dirigente/cadastro/'.$dados['id']);
    }

    public function gravaDocumentoRG(Request $request)
    {
        $dados = $request->all();

        if (array_key_exists('documento_rg',$dados)) {
            $img = $request->file('documento_rg'); //recebe a imagem
            $ext = pathinfo($img->getClientOriginalName(), PATHINFO_EXTENSION) ?: '';
            $img_nome = 'D-'.str_pad($dados['id'], 5, "0", STR_PAD_LEFT).'_rg.'.$ext;  //nome da imagem
            $img->move(public_path().'/imagens', $img_nome); //move a imagem para pasta

            $documento = $this->dirigenteDocumento->where('dirigente_id',$dados['id'])->first();

            if ($documento == null){
                $this->dirigenteDocumento->insert([
                    'dirigente_id' => $dados['id'],
                    'documento_rg' => $img_nome
                ]);
            }
            else {
                $documento->update([
                    'documento_rg' => $img_nome
                ]);
            }

        }  else {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Não foi fornecida uma imagem ';
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        }
        return redirect('/dirigente/cadastro/'.$dados['id']);
    }

    public function gravaDocumentoCR(Request $request)
    {
        $dados = $request->all();

        if (array_key_exists('documento_cr',$dados)) {
            $img = $request->file('documento_cr'); //recebe a imagem
            $ext = pathinfo($img->getClientOriginalName(), PATHINFO_EXTENSION) ?: '';
            $img_nome = 'D-'.str_pad($dados['id'], 5, "0", STR_PAD_LEFT).'_cr.'.$ext;  //nome da imagem
            $img->move(public_path().'/imagens', $img_nome); //move a imagem para pasta

            $documento = $this->dirigenteDocumento->where('dirigente_id',$dados['id'])->first();

            if ($documento == null){
                $this->dirigenteDocumento->insert([
                    'dirigente_id' => $dados['id'],
                    'documento_cr' => $img_nome
                ]);
            }
            else {
                $documento->update([
                    'documento_cr' => $img_nome
                ]);
            }

        }  else {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Não foi fornecida uma imagem ';
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        }
        return redirect('/dirigente/cadastro/'.$dados['id']);
    }

    public function removeDocumentoFoto($idJogador) {

        try {
            $docJogador = $this->dirigenteDocumento->where('dirigente_id','=', $idJogador)->first();
            $arquivo = public_path().'/imagens/'.$docJogador->foto_dirigente;
            unlink($arquivo);
            $docJogador->foto_dirigente = null;
            $docJogador->save();
            return redirect('/dirigente/cadastro/'.$idJogador);
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
            $docJogador = $this->dirigenteDocumento->where('dirigente_id','=', $idJogador)->first();
            $docJogador->documento_cpf = null;
            $docJogador->save();
            return redirect('/dirigente/cadastro/'.$idJogador);
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
            $docJogador = $this->dirigenteDocumento->where('dirigente_id','=', $idJogador)->first();
            $docJogador->documento_rg = null;
            $docJogador->save();
            return redirect('/dirigente/cadastro/'.$idJogador);
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
            $docJogador = $this->dirigenteDocumento->where('dirigente_id','=', $idJogador)->first();
            $docJogador->documento_cr = null;
            $docJogador->save();
            return redirect('/dirigente/cadastro/'.$idJogador);
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
        $bgball = public_path('imagens').'/'.'versodirigentebg.jpg';
        $bgball = $this->converimaget($bgball);

        $equipe = null;
        $registro = null;
        $jogador = $this->dirigente->find($id);
        #$pathImagem = Parametro::where('name','path_imagens')->first()->value;
        #if (!$pathImagem)
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

            $documento = $this->dirigenteDocumento->where('dirigente_id','=',$jogador->id)->first();
            if ($documento == null || $documento->foto_dirigente == '')  {
                $foto = $pathImagem.'No-Image-Person.jpg';
                $foto = $this->converimaget($foto);
            } else {
                $foto = $pathImagem.$documento->foto_dirigente;
                $foto = $this->converimaget($foto);

            }

            $html = view('dirigentes.dirigente-carteirinha',compact('jogador','foto','logo','equipe',
                'bgball','registro', 'validade'))->render();
            //return $html;
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
