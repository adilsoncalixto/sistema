<?php

namespace App\Services;


use App\Models\Equipe;
use App\Models\Sumula;
use App\Models\SumulaEquipes;
use App\Models\SumulaJogadores;

class SumulaService
{

    /**
     * @var Sumula
     */
    private $sumula;
    /**
     * @var SumulaEquipes
     */
    private $sumulaEquipes;
    /**
     * @var SumulaJogadores
     */
    private $sumulaJogadores;
    /**
     * @var Equipe
     */
    private $equipe;

    public function __construct(Sumula $sumula, SumulaEquipes $sumulaEquipes,
                                SumulaJogadores $sumulaJogadores, Equipe $equipe)
    {

        $this->sumula = $sumula;
        $this->sumulaEquipes = $sumulaEquipes;
        $this->sumulaJogadores = $sumulaJogadores;
        $this->equipe = $equipe;
    }

    public function calculaPlacar($idSumula)
    {

        $totalGolsEquipe1  = 0;
        $totalGolsEquipe2  = 0;
        $idEquipeVencedora = -1;
        $nomeEquipeVencedora = '';

        $tblSumula = $this->sumula->find($idSumula);
        if ($tblSumula) {
            $tblSumulaEquipe1 = $this->sumulaEquipes->byEquipe($idSumula, 1)->first();
            if ($tblSumulaEquipe1) {
                $sumulaJogadores1 = $this->sumulaJogadores->byEquipe($idSumula, 1)->get();

                foreach ($sumulaJogadores1 as $jogador) {
                    $totalGolsEquipe1 += $jogador->gols;
                }
                $idEquipeVencedora = $tblSumulaEquipe1->equipe_id;

            }
            $tblSumulaEquipe2 = $this->sumulaEquipes->byEquipe($idSumula, 2)->first();
            if ($tblSumulaEquipe2) {
                $sumulaJogadores2 = $this->sumulaJogadores->byEquipe($idSumula, 2)->get();

                foreach ($sumulaJogadores2 as $jogador) {
                    $totalGolsEquipe2 += $jogador->gols;
                   // echo ($totalGolsEquipe2.'<br>');
                }
                if ($totalGolsEquipe2 > $totalGolsEquipe1) {
                    $idEquipeVencedora = $tblSumulaEquipe2->equipe_id;
                } else if  ($totalGolsEquipe2 == $totalGolsEquipe1) {
                    $idEquipeVencedora = 0;
                    }

            }
            if ($idEquipeVencedora != 0) {
                $equipe = $this->equipe->find($idEquipeVencedora);
                if ($equipe) {
                    $nomeEquipeVencedora = $equipe->nome_equipe;
                }
            } else $nomeEquipeVencedora = 'EMPATE';
        }
        return $totalGolsEquipe1.';'.$totalGolsEquipe2.';'.$idEquipeVencedora.';'.$nomeEquipeVencedora;
    }

}