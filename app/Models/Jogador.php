<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jogador extends Model
{
    protected $table = 'jogadores';


    public function scopeJogadoresByEquipe($query, $equipe)
    {
        $query->where('equipe_id','=', $equipe);
        return $query;
    }

    public function scopeLiberadosByEquipes($query, $equipe)
    {
        $query->where('equipe_id',$equipe)->where('liberado',1)->orderBy('nome_jogador');
        return $query;
    }

    public function scopeLiberados($query)
    {
        $query->where('liberado',1);
        return $query;
    }

    public function scopeOrderByNome($query)
    {
        $query->orderBy('nome_jogador');
        return $query;
    }

    public function scopeJogadorByCategoria($query, $categoria_id)
    {
        $query->where('categoria_id', '=', $categoria_id);
        return $query;
    }

}
