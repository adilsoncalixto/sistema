<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $fillable = [

        'competicao_id',
        'categoria_id',
        'inicio',
        'termino',
        'local_id',
        'equipe1_id',
        'equipe2_id',
        'arbitro1_id',
        'arbitro2_id',
        'arbitro3_id',
        'historico',
        'vencedor',
        'rodada'
    ];

    public function scopeListAll($query)
    {
        $sql = $query->select('partidas.id as id',
            'partidas.competicao_id as id_competicao',
            'competicoes.nome',
            'partidas.local_id',
            'locais.nome as nomelocal',
            'partidas.inicio',
            'partidas.termino',
            'eq1.nome_equipe as equipe1',
            'eq2.nome_equipe as equipe2')
            ->join('competicoes', 'competicoes.id', '=', 'partidas.competicao_id')
            ->leftJoin('locais', 'locais.id', '=', 'partidas.local_id')
            ->leftJoin('equipes as eq1','eq1.id','=','partidas.equipe1_id')
            ->leftJoin('equipes as eq2','eq2.id','=','partidas.equipe2_id');

        return $sql;

    }

    public function scopeListByCompeticao($query, $competicao)
    {
        $sql = $query->select('partidas.id as id',
            'partidas.competicao_id as id_competicao',
            'competicoes.nome',
            'partidas.local_id',
            'locais.nome as nomelocal',
            'partidas.inicio',
            'partidas.termino')
            ->join('competicoes', 'competicoes.id', '=', 'partidas.competicao_id')
            ->leftJoin('locais', 'locais.id', '=', 'partidas.local_id');

        if ($competicao > 0)
            $sql->where('competicoes.id',$competicao);

        return $sql;

    }

    public function scopeByCompeticaoPartida($query, $competicao, $partida)
    {
        $query->select('partidas.*','eq1.nome_equipe as equipe1','eq2.nome_equipe as equipe2')
            ->join('competicoes', 'competicoes.id', '=', 'partidas.competicao_id')
            ->join( 'equipes as eq1', 'eq1.id', '=', 'partidas.equipe1_id')
            ->join( 'equipes as eq2', 'eq2.id', '=', 'partidas.equipe2_id')
            ->where('partidas.competicao_id', '=', $competicao)
            ->where('partidas.local_id', '=', $partida);

        return $query;
    }

    public function scopeListByCompeticao1($query, $competicao)
    {
        $sql = $query->select('locais.nome as nomelocal','partidas.id as id')
            ->join('competicoes', 'competicoes.id', '=', 'partidas.competicao_id')
            ->leftJoin('locais', 'locais.id', '=', 'partidas.local_id');

        if ($competicao > 0)
            $sql->where('competicoes.id',$competicao);

        return $sql;

    }

    public function scopeValidaLocalHorario($query, $local, $horario)
    {
        $query->where('local_id','=',$local)->where('inicio','=',$horario);

        return $query;
    }

    public function scopeDadosPartidas($query, $partida)
    {
        $query->select(
            'partidas.id as id',
            'partidas.equipe1_id',
            'equipe1.categoria_id as id_categoria',
            'equipe1.nome_equipe as equipe1',
            'partidas.equipe2_id',
            'equipe2.nome_equipe as equipe2',
            'partidas.arbitro1_id',
            'partidas.arbitro2_id',
            'partidas.arbitro3_id',
            'partidas.categoria_id',
            'partidas.rodada'
        )
        ->join('equipes as equipe1','equipe1.id','=','partidas.equipe1_id')
        ->join('equipes as equipe2','equipe2.id','=','partidas.equipe2_id')
        ->where('partidas.id','=',$partida);

        return $query;
    }
}
