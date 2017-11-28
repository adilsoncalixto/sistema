<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Usuario extends Model
{
    protected $table = 'usuarios';

    public function getAll() {
        $usuarios = DB::table('usuarios')
            ->join('users', 'usuarios.user_id', '=', 'users.id')
            ->join('tipo_usuarios', 'usuarios.tipo_id', '=', 'tipo_usuarios.id')
            ->leftjoin('equipes', 'usuarios.equipe_id', '=', 'equipes.id')
            ->get();

        return $usuarios;

    }

    public function getById($idUsuario) {
        $usuarios = DB::table('usuarios')
            ->join('users', 'usuarios.user_id', '=', 'users.id')
            ->join('tipo_usuarios', 'usuarios.tipo_id', '=', 'tipo_usuarios.id')
            ->leftjoin('equipes', 'usuarios.equipe_id', '=', 'equipes.id')
            ->where('usuarios.user_id',$idUsuario)->first();
        return $usuarios;

    }

    public function findById($id) {
        $usuarios = DB::table('usuarios')->where('user_id', $id)->first();
        return $usuarios;
    }

    public function updateDados($idUsr,$tipo,$equipe) {
        $usuarios = $this->getById($idUsr);
        if ($usuarios != null) {
            DB::table('users')->where('user_id', $idUsr)->update(['tipo_id' => $tipo])->update(['equipe_id' => $equipe]);
        }

        return true;
    }
}
