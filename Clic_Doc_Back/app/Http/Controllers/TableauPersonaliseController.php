<?php

namespace App\Http\Controllers;

use App\Models\TableauPersonalise;
use Illuminate\Http\Request;

class TableauPersonaliseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TableauPersonalise::where("patient_id",'=',request()->patient_id)->get();
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
        $tp = new TableauPersonalise();
        $tp -> indication = request()->indication;
        $tp -> date = request() -> date;
        $tp -> laser = request()->laser;
        $tp -> pore = request()->pore;
        $tp -> longueur_onde = request()->longueur_onde;
        $tp ->pm = request()->pm;
        $tp ->fu = request()->fu;
        $tp ->lt = request()->lt;
        $tp ->Note = request() -> Note;
        $tp ->patient_id = request()->patient_id;
        $tp ->save();
        return $tp;

    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TableauPersonalise $tableauPersonalise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TableauPersonalise $tableauPersonalise)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tp = TableauPersonalise::find($id);
        $tp ->delete();

        return ["message"=>"Supprimé avec succes"];
    }
}
