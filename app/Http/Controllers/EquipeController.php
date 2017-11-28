<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

use App\Models\Equipe;
use App\User;
use Bican\Roles\Models\Role;
use DB;

class EquipeController extends Controller
{
    /**
     * @var Equipe
     */
    private $equipe;

    public function __construct(Equipe $equipe)
    {

        $this->equipe = $equipe;
    }

    public function index() {

       /* $equipes = DB::table('equipes')->select('equipes.id', 'equipes.nome_equipe','categorias.nome_categoria','equipes.email_equipe')
            ->join('categorias', 'equipes.categoria_id', '=', 'categorias.id')
            ->get();*/
       $equipes = $this->equipe->listAll()->orderByNome()->get();

        return view('equipes.equipes-lista',compact('equipes'));
    }

    public function cadastro(Request $request,$idRegistro = -1) {

        if ($request->method() == 'GET') {

            if ($idRegistro > 0) {
                $equipe = $this->equipe->find($idRegistro);

            } else {
                $equipe = null;
            }

            return view('equipes.equipes-cadastro', compact('equipe'));
        }
        elseif ($request->method() == 'POST') {


            $id         = $request->input('id');
            $nomeEquipe = $request->input('nomeequipe');
            $email      = $request->input('email');
            $senha      = str_random(6);
            $acao       = 'alteracao';
            $idRegistro = $id;

            try {

                $equipe = $this->equipe->find($idRegistro);

                if ($equipe == null) {
                    $acao = 'inclusao';
                    $user = User::where('email','=', $email)->first();
                    if ($user == null) {
                        $user = new User;
                    }

                    $equipe = new Equipe;

                    $user->name = $nomeEquipe;
                    $user->email = strtolower($email);
                    $user->password =  bcrypt($senha);
                    $user->save();
                    $idUsr = $user->id;

                    $role = Role::where('slug','=','equipe')->first();
                    if ($role == null) {
                        $role = Role::where('slug','=',Config::get('app.role_padrao'))->first();
                    }
                    $user->attachRole($role);
                } else {
                    $user = User::where('email','=', $email)->first();
                    if ($user == null) {
                        $user = new User;
                        $user->name = $nomeEquipe;
                        $user->email = strtolower($email);
                        $user->password =  bcrypt($senha);
                        $user->save();
                        $idUsr = $user->id;
                    } else {
                        $user->email = strtolower($email);
                        $user->save();
                    }
                }

                $equipe->nome_equipe   = $nomeEquipe;
                $equipe->email_equipe  = strtolower($email);
                $equipe->congresso     = ($request->input('congresso') == null) ? 0 : 1;
                $equipe->abertura      = ($request->input('abertura') == null) ? 0 : 1;

                $equipe->save();
                $idEquipe = $equipe->id;

                $role = Role::where('slug','=','equipe')->first();
                if ($role == null) {
                    $role = Role::where('slug','=',Config::get('app.role_padrao'))->first();
                }
                $user->attachRole($role);


                if ($acao == 'inclusao') {
                    $email = $equipe->email_equipe;

                    Mail::send('equipes.equipe-email-cadastro', compact('email','senha'), function($message) use ($email) {
                        $message->from('mailsender@hortofutsal.com.br', 'LHFS');
                        $message->to($email, '')->subject('cadastro realizado');
                    });
                }

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A gravação do registro foi realizada com sucesso';
                $botoes = '<a href="/equipe/cadastro" class="btn btn-outline btn-primary">Novo</a>';
                $botoes = $botoes.' <a href="/equipe/lista" class="btn btn-outline btn-primary">Lista</a>';

            } catch (\Exception $e) {
                $corPainel = 'panel-red';
                $txtTitulo = 'Erro';
                $txtMensagem = 'Ocorreu um erro na gravação do registro '. $e->getMessage();
                $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            }

            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));

        }
    }

    public function exclui(Request $request) {

        try {

            $data = $request->input();

            $idRegistro = $data['idexclui'];

            $equipe = $this->equipe->find($idRegistro);
            $equipe->delete();

            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
            $botoes = '<a href="/equipe/categoria" class="btn btn-outline btn-primary">Nova</a>';
            $botoes = $botoes.' <a href="/equipe/lista" class="btn btn-outline btn-primary">Lista</a>';

        } catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            if (strpos($e->getMessage(), 'a foreign key constraint fails (`liga_hortolandia`.`jogadores`, CONSTRAINT `jogadores_equipe_id_foreign') > 0) {
                $txtMensagem = 'Existe jogadores cadastrados nesta equipe';
            } else if (strpos($e->getMessage(), 'a foreign key constraint fails') > 0) {
                $txtMensagem = 'Existe dirigentes cadastrados nesta equipe';
            } else $txtMensagem = 'Ocorreu um erro na exclusão do registro '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }
}
