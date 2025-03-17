<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(request()->has("date"))
        {
            return Agenda::whereDate("date",Carbon::parse(request()->date)->format('Y-m-d'))
            ->orderBy("heure",'asc')
            ->get();
        }
        if(request()->has("month"))
        {
            return Agenda::whereMonth("date",request()->month)
            ->orderBy("heure",'asc')
            ->get();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $agenda = new Agenda();
        $agenda -> libelle = request()->libelle;
        $agenda -> date = request()->date;
        $agenda -> heure = request()->heure;
        $agenda -> is_controle = request()->is_controle;
        $agenda -> doctor_id = auth()->user()->id;
        $agenda -> save();

        return $agenda;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Agenda::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $agenda = Agenda::find($id);
        $agenda -> delete();

        return ["message"=>"Suppression avec succès"];

    }
}
