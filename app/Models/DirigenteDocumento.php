<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirigenteDocumento extends Model
{
    protected $table = "dirigente_documentos";

    protected $fillable = [
       'documento_cpf',
       'documento_rg',
       'documento_cr',
       'foto_dirigente'
    ];


    public function scopeGetByDirigente($query, $id)
    {
        $sql = $query->where('dirigente_id',$id);
        dd($sql->toSql());
        return $sql;
    }

}
