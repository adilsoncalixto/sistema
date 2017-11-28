<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HistoricoPartida extends Model
{
	protected $table = 'historico_partidas';
     protected $fillable = [
        'id_sumula',
        'id_jogador',
        'id_equipe',
        'id_competicao',
        'id_categoria',
        'id_sub',
        'camisa',
        'type_param',
        'type_form',
        'tempo',
        'id_partida',
        'rodada'
    ];

  public function scopeCountGolsEquipe($query, $id_equipe,$id_sumula)
  {
      return  $query
      ->select(
          DB::raw('
                  (select count(1)
                  from 
                  historico_partidas 
                  where historico_partidas.type_form="GF" and
                  historico_partidas.id_sumula = '.$id_sumula.' and
                  historico_partidas.id_equipe = '.$id_equipe.'
                  )  as GF'),
                  DB::raw('
                  (select count(1)
                  from 
                  historico_partidas 
                  where historico_partidas.type_form="GC" and
                  historico_partidas.id_sumula = '.$id_sumula.' and
                  historico_partidas.id_equipe = '.$id_equipe.'
                  ) as GC')
                )
      ->groupBy('GF')
      ->get();
  }

    public function artilharia($competicao, $categoria)
    {
        $retorno = DB::select(DB::raw('
                    select
                       q1.id_jogador as registro,
                       jd.foto_jogador,
                       j.nome_jogador,
                       eq.nome_equipe as equipe,
                       q1.gols
                    FROM
                    (
                      SELECT
                        id_jogador,
                        count(*) as gols
                      FROM  historico_partidas
                      WHERE id_competicao = '.$competicao.'
                        AND id_categoria = '.$categoria.'
                      GROUP BY id_jogador
                    ) as q1
                    JOIN jogadores j on j.id = q1.id_jogador
                    join jogador_documentos jd on jd.jogador_id = q1.id_jogador
                    JOIN equipes eq on eq.id = j.equipe_id
                    order by gols DESC, j.nome_jogador
                    ')
                );


        return $retorno;
    }

    public function sub($type_form, $id_jogador)
    {
        $retorno = DB::select('select historico_partidas.id, jogadores.nome_jogador as tempo, historico_partidas.type_form as type_form
                                from jogadores
                                inner join historico_partidas on 
                                historico_partidas.id_sub = jogadores.id
                                where 
                                historico_partidas.type_form = "'.$type_form.'"
                                and historico_partidas.id_jogador = '.$id_jogador.''
        );


        return $retorno;
    }
}
