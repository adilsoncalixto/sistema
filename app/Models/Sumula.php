<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sumula extends Model
{
    protected $fillable = [
        'competicao_id',
        'partida_id',
        'data_partida',
        'serie',
        'fase',
        'anotador',
        'cronometrista',
        'periodo_1_inicio',
        'periodo_1_termino',
        'periodo_2_inicio' ,
        'periodo_2_termino',
        'periodo_3_inicio' ,
        'periodo_3_termino',
        'periodo_4_inicio' ,
        'periodo_4_termino',
        'marcador_1_equipe1',
        'marcador_1_equipe2',
        'marcador_2_equipe1',
        'marcador_2_equipe2',
        'marcador_3_equipe1',
        'marcador_3_equipe2',
        'marcador_4_equipe1',
        'marcador_4_equipe2',
    ];

    public function scopeLista($query)
    {
        $query->select('sumulas.*','competicoes.nome as nomecompeticao',
            'partidas.inicio as datapartida','locais.nome as local','equipe1.nome_equipe as nomeequipe1','equipe2.nome_equipe as nomeequipe2')
            ->join('competicoes','competicoes.id','=','sumulas.competicao_id')
            ->join('partidas','partidas.id','=','sumulas.partida_id')
            ->join('locais','locais.id','=','partidas.local_id')
            ->join('sumula_equipes as se1',function ($join) {
                $join->on('se1.sumula_id','=','sumulas.id')->where('se1.equipe_lado','=',1);
            })
            ->join('sumula_equipes as se2',function ($join) {
                $join->on('se2.sumula_id','=','sumulas.id')->where('se2.equipe_lado','=',2);
            })
           ->join('equipes as equipe1', function ($join) {
                $join->on('equipe1.id','=','se1.equipe_id');
            })
            ->join('equipes as equipe2', function ($join) {
                $join->on('equipe2.id','=','se2.equipe_id');
            });

        return $query;
    }

    public function scopeByCompeticaoPartida($query, $competicao, $partida)
    {
        $query->where('competicao_id','=',$competicao)->where('partida_id','=',$partida);

        return $query;
    }
}
