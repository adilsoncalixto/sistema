<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arbitro extends Model
{
    protected $fillable = [
        'nome',
        'cpf',
        'rg',
        'telefone',
        'celular'
    ];
}
