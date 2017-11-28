<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    protected $table = 'equipes';

    protected $fillable = [
        'nome_equipe',
        'email_equipe',
        'congresso',
        'abertura'
    ];

    public function scopeListAll($query)
    {
        $query->select('equipes.*');
        return $query;
    }

    public function scopeOrderByNome($query)
    {
        $query->orderBy('nome_equipe');
        return $query;
    }
}
