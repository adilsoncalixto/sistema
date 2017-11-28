<?php

namespace App\Http\Controllers;

use App\Models\Role_user;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Usuario;
use App\Models\Equipe;
use App\User;

use Bican\Roles\Models\Role;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class UsuarioController extends Controller
{
    /**
     * @var Role_user
     */
    private $role_user;
    /**
     * @var Role
     */
    private $role;

    public function __construct(
        Role_user $role_user,
        Role $role
    )
    {

        $this->role_user = $role_user;
        $this->role = $role;
    }

    public function index() {

        $usuarios = User::leftjoin('role_user','role_user.user_id','=','users.id')
            ->leftjoin('roles','roles.id','=','role_user.role_id')
            ->select('users.id',
                'users.name as nome_usuario',
                'users.email',
                'roles.name as tipo',
                'roles.slug')
            ->get();

        return view('usuarios.usuarios-lista',compact('usuarios'));
    }

    public function cadastro(Request $request,$idUsuario = -1) {

        if ($request->method() == 'GET') {

            $tipoUsuarios = Role::all();
            $equipes      = Equipe::get();
            $usuario = User::where('users.id','=',$idUsuario)
                ->leftjoin('role_user','role_user.user_id','=','users.id')
                ->leftjoin('roles','roles.id','=','role_user.role_id')
                ->select('users.id',
                    'users.name as nome_usuario',
                    'users.email',
                    'roles.name as tipo',
                    'roles.slug')
                ->first();

            return view('usuarios.usuarios-cadastro', compact('tipoUsuarios', 'equipes', 'usuario'));
        }
        elseif ($request->method() == 'POST') {

            $input = $request->all();

            $idUsr  = $input['id'];

            try {

                $user = User::find($idUsr);

                if ($user == null) {
                    $user = new User;
                }

                $user->name = $input['nomecompleto'];
                $user->email = strtolower($input['email']);
                $user->save();

                $role = Role::where('slug','=',$request->input('tipousuario'))->first();
                if ($role == null) {
                    $role = Role::where('slug','=',Config::get('app.role_padrao'))->first();
                }
                $user->attachRole($role);

                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A gravação do registro foi realizada com sucesso';
                $botoes = '<a href="/usuario/cadastro" class="btn btn-outline btn-primary">Novo</a>';
                $botoes = $botoes.' <a href="/usuario/lista" class="btn btn-outline btn-primary">Lista</a>';

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

            if ($idRegistro == 1) {
                $corPainel = 'panel-red';
                $txtTitulo = 'Erro';
                $txtMensagem = 'Usuário administrador não pode ser excluido';
                $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
                return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
            }

            $user = User::find($idRegistro);

            $user->detachAllRoles();
            $user->delete();

            $corPainel = 'panel-green';
            $txtTitulo = 'Sucesso';
            $txtMensagem = 'A exclusão do registro foi realizada com sucesso';
            $botoes = '<a href="/usuario/categoria" class="btn btn-outline btn-primary">Nova</a>';
            $botoes = $botoes.' <a href="/usuario/lista" class="btn btn-outline btn-primary">Lista</a>';

        } catch (\Exception $e) {
            $corPainel = 'panel-red';
            $txtTitulo = 'Erro';
            $txtMensagem = 'Ocorreu um erro na exclusão do registro '. $e->getMessage();
            $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
        }

        return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
    }

    public function reset(Request $request)
    {
        $dados = $request->all();
        $user = User::find($dados['idreset']);
        if ($user->is('equipe')) {
            $senha = str_random(6);
            $user->password = bcrypt($senha);
            $user->save();
            $email = $user->email;
            $resetok = true;

            Mail::send('equipes.equipe-email-reset', compact('email','senha'), function($message) use ($email) {
                $message->from('mailsender@hortofutsal.com.br', 'LHFS');
                $message->to($email, '')->subject('Reset de senha');
            });
        }

        return redirect('/usuario/lista')->with('status', 'Senha resetada com sucesso!');
    }

    public function meusDados(Request $request)
    {
        $dados = $request->all();

        $user = User::find($dados['idusr']);
        $role_usr = $this->role_user->where('user_id','=',$dados['idusr'])->first();
        $role = Role::where('id',$role_usr->role_id)->first();
        $tipocad = $role->name;

        return view('usuarios.usuarios-meusdados', compact('tipocad'));

    }

    public function trocaSenha(Request $request)
    {
        $dados = $request->all();
      //  dd($dados);
        $user = User::find($dados['id']);
        if ($user) {
            if ($dados['senha'] == $dados['comfirma']) {
                $user->password = bcrypt($dados['senha']);
                $user->save();
                $corPainel = 'panel-green';
                $txtTitulo = 'Sucesso';
                $txtMensagem = 'A senha foi alterada  com sucesso';
                $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            } else {
                $corPainel = 'panel-red';
                $txtTitulo = 'Erro';
                $txtMensagem = 'As senhas digitadas não conferem';
                $botoes = '<a href="javascript:history.back(-1)" class="btn btn-outline btn-primary">OK</a>';
            }
            return view('aviso',compact('corPainel','txtTitulo','txtMensagem','botoes'));
        };
    }

}
