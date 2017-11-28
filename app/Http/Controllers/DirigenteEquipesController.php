<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Dirigente;
use App\Models\Equipe;
use App\Models\Dirigente_equipe;

class DirigenteEquipesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($dirigente = 0)
    {
        $dirigentes = array('0' => 'Selecione') + Dirigente::lists('nome_dirigente','id')->toArray();
        $equipes    = array('0' => 'Selecione') + Equipe::lists('nome_equipe','id')->toArray();
        $lista      = Dirigente_equipe::select(
            'dirigente_equipes.id',
            'dirigente_equipes.dirigente_id',
            'dirigente_equipes.equipe_id',
            'equipes.nome_equipe')
            ->join('equipes','equipes.id','=','dirigente_equipes.equipe_id')
            ->where('dirigente_equipes.dirigente_id','=',$dirigente)
            ->get();

        $equipe    = 0;
        return view('dirigentes.dirigente-equipes', compact('dirigentes','dirigente','equipes','equipe','lista'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $dirigenteEquipe = Dirigente_equipe::where('dirigente_id','=',$input['dirigente'])
            ->where('equipe_id','=',$input['equipe'])
            ->first();

        if ($dirigenteEquipe == null) {
            $dirigenteEquipe = new Dirigente_equipe;
        }

        $dirigenteEquipe->dirigente_id = $input['dirigente'];
        $dirigenteEquipe->equipe_id    = $input['equipe'];

        $dirigenteEquipe->save();

        return redirect('/dirigente/equipes/'.$dirigenteEquipe->dirigente_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id=0)
    {
        $dirigenteEquipe = Dirigente_equipe::where('id', '=', $id)->first();

        $dirigentes = array('0' => 'Selecione') + Dirigente::lists('nome_dirigente','id')->toArray();
        $equipes    = array('0' => 'Selecione') + Equipe::lists('nome_equipe','id')->toArray();

        $dirigente = $dirigenteEquipe->dirigente_id;
        $equipe    = $dirigenteEquipe->equipe_id;

        $lista      = Dirigente_equipe::select(
            'dirigente_equipes.id',
            'dirigente_equipes.dirigente_id',
            'dirigente_equipes.equipe_id',
            'equipes.nome_equipe')
            ->join('equipes','equipes.id','=','dirigente_equipes.equipe_id')
            ->where('dirigente_equipes.dirigente_id','=', $dirigente)
            ->get();



        return view('dirigentes.dirigente-equipes', compact('dirigentes','dirigente','equipes','equipe','lista'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id=0)
    {
        $dirigenteEquipe = Dirigente_equipe::where('id', '=', $id)->first();
        $dirigente = $dirigenteEquipe->dirigente_id;
        $dirigenteEquipe->delete();
        return redirect('/dirigente/equipes/'.$dirigente);
    }
}
