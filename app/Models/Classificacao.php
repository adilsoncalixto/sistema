<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classificacao extends Model
{
    protected $table = 'vw_classificacao';

    public function scopeListaClassificacao($query, $competicao, $categoria)
    {
        $query->where('id_competicao','=', $competicao)->where('id_categoria','=', $categoria);
    }


}

