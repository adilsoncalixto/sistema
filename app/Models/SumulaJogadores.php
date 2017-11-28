<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumulaJogadores extends Model
{
    protected $table = 'sumula_jogadores';

    protected $fillable = [
        'gols',
        'faltas',
        'amarelo',
        'vermelho'
    ];

    public function scopeByEquipe($query,$sumula, $equipe)
    {
        $query->where('sumula_id','=', $sumula)
            ->where('equipe_id','=', $equipe)
            ->orderBy('jogador_id');

        return $query;
    }

}
