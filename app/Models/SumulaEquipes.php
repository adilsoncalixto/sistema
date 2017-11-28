<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumulaEquipes extends Model
{
    protected $table = 'sumula_equipes';

    protected $fillable = [
        'sumula_id',
        'equipe_lado',
        'equipe_id',
        'massagista',
        'dirigente_id'
    ];


    public function scopeByEquipe($query, $id, $equipe)
    {
        $query->where('sumula_id','=', $id)
            ->where('equipe_lado','=', $equipe);

        return $query;
    }

    public function scopeGetDirigenteEquipe($query, $id, $equipe)
    {
        $query->where('sumula_equipes.sumula_id','=', $id)
            ->where('sumula_equipes.equipe_id','=', $equipe)
            ->join('dirigentes','dirigentes.id','=','sumula_equipes.dirigente_id');

        return $query;
    }
}
