<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competicao extends Model
{
    protected $table = 'competicoes';

    protected $fillable = [
        'nome',
        'inicio',
        'termino',
        'locais',
        'historico',
        'campeao'
    ];


    public function scopeListLocais($query, $competicao)
    {
        $query->where('id', '=', $competicao);

        return $query;
    }
}
