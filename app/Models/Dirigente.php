<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dirigente extends Model
{
    protected $table = 'dirigentes';

    protected $fillable = [
        'equipe',
        'equipe_id',
        'nome_dirigente',
        'dirigente_tipo_id',
        'cargo',
        'cpf_dirigente',
        'rg_dirigente',
        'telefone_dirigente',
        'celular_dirigente',
        'data_nascimento',
        'endereco_dirigente',
        'email_dirigente',
        'data_validade',
        'analizando',
        'liberado'
    ];


    public function scopeByEquipe($query, $equipe = -1)
    {
        $query->select('dirigentes.*','dirigente_tipos.nome as tipo')
            ->join('dirigente_tipos','dirigente_tipos.id','=','dirigentes.dirigente_tipo_id')
            ->where('dirigentes.equipe_id', '=', $equipe);
        return $query;
    }

    public function scopeOrderByNome($query)
    {
        $query->orderBy('nome_dirigente');
        return $query;
    }

    public function scopeLiberados($query)
    {
        $query->where('liberado',1);
        return $query;
    }
}
