<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    protected $table = 'locais';

    protected $fillable = [
        'nome',
        'endereco',
        'referencia'
    ];

    public function scopeList($query, $locais)
    {
        $count = 1;
        $query->select('locais.*');

        foreach ($locais as $local) {
            if ($count == 1) $query->where('id','=',$local);
            else $query->orWhere('id','=',$local);
            $count++;
        }

        return $query;
    }

    public function scopeOrderByLocal($query)
    {
        $query->orderBy('nome');

        return $query;
    }
}
