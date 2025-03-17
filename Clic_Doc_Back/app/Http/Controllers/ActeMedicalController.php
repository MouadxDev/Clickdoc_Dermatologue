<?php

namespace App\Http\Controllers;

use App\Models\ActeMedical;
use Illuminate\Http\Request;

class ActeMedicalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = ActeMedical::where("entity_id","=",auth()->user()->entity_id);
        if(request()->has("toGet"))
            return $model->paginate(request()->toGet);
        else
        {
            return $model->get();
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
        $am = new ActeMedical();
        $am->libelle = request()->libelle;
        $am->prix = request()->prix;
        $am->entity_id = auth()->user()->entity_id;
        $am->save();

        return $am;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return ActeMedical::find($id);
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
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $am = ActeMedical::find($id);
        $am -> delete();

        return ["message"=>"Supprimé avec succès"];
    }
}
