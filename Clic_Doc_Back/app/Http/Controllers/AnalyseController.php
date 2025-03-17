<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Consultation;
use App\Models\DemandeAnalyse;
use App\Models\Entite;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class AnalyseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = new Analyse;
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
        $model = new Analyse();
        $model -> libelle = request()->libelle; 
        $model -> save() ;

        return $model;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $model = Analyse::find($id);
        $model -> delete();

        return ["message"=>"Supprimé avec succès"];
    }

    public function imprimer(string $id)
    {
        $data["analyses"] = DemandeAnalyse::join("analyses as a","a.id",'=',"demande_analyses.analyse_id")
        ->where("demande_analyses.consultation_id","=",$id)
        ->select("a.libelle")
        ->get();
        $consult = Consultation::find($id);
        $data["patient"] = Patient::find($consult->patient_id);
        $data["docteur"] = User::find($consult->doctor_id);
        $data["entite"] = Entite::find($data["docteur"]->entity_id);

        return view("analyses",$data);
    }
}
