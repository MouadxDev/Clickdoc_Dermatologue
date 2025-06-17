<?php

namespace App\Http\Controllers;

use App\Models\DemandeAnalyse;
use App\Models\ClicklabJoin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DemandeAnalyseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DemandeAnalyse::join("consultations as c","consultation_id","=","c.id")
        ->join("analyses as a","analyse_id","=","a.id")
        ->select("demande_analyses.*","c.doctor_id","a.libelle")
        ->where("doctor_id","=",auth()->user()->id)
        ->get();
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
        // Check if analyse_id is null or empty and new_analyse is provided
        $analyseId = $request->analyse_id;
        $newAnalyseName = $request->new_analyse;
    
        if (empty($analyseId) && !empty($newAnalyseName)) {
            // Create the new Analyse
            $newAnalyse = new \App\Models\Analyse(); // Adjust namespace as needed
            $newAnalyse->libelle = $newAnalyseName;
            $newAnalyse->save();
    
            // Use the new ID for the demande
            $analyseId = $newAnalyse->id;
        }
    
        $model = new DemandeAnalyse();
        $model->consultation_id = $request->consultation_id;
        $model->analyse_id = $analyseId;
        $model->state = "soumise";
        $model->save();
    
        // Generate or retrieve the unique key for the consultation
        $consultationId = $model->consultation_id;
        $clilcklabJoin = ClicklabJoin::where('consultation_id', $consultationId)->first();
    
        if (!$clilcklabJoin) {
            $uniqueKey = null;
    
            do {
                $prefix = 'AN';
                $randomNumber = mt_rand(100000, 999999);
                $uniqueKey = "{$prefix}-{$randomNumber}";
            } while (ClicklabJoin::where('unique_key', $uniqueKey)->exists());
    
            $clilcklabJoin = new ClicklabJoin();
            $clilcklabJoin->consultation_id = $consultationId;
            $clilcklabJoin->unique_key = $uniqueKey;
            $clilcklabJoin->save();
        }
    
        return [
            'demande_analyse' => $model,
            'clilcklab_join' => $clilcklabJoin
        ];
    }
    
    
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return DemandeAnalyse::join("analyses as a","a.id","=","demande_analyses.analyse_id")
        ->where("demande_analyses.consultation_id","=",$id)
        ->select("a.libelle","demande_analyses.*")
        ->get();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if(auth()->user()->role=="Admin" OR auth()->user()->role=="doctor")
            $doctor = auth()->user();
        else 
            $doctor = User::where("entity_id","=",auth()->user()->entity_id)->whereIn("role",["Admin","doctor"])->first();

        return DemandeAnalyse::join("analyses as a","a.id","=","demande_analyses.analyse_id")
        ->join("consultations as c","c.id","=","demande_analyses.consultation_id")
        ->where("c.patient_id","=",$id)
        ->where("c.doctor_id","=",$doctor->id)
        ->select("a.libelle","demande_analyses.*")
        ->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $model = DemandeAnalyse::find($id);
        $model->lab_id = request()->lab_id;
        $model->state = request()->state;
        $model->document = request()->document;
        $model->save();

        return $model;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = DemandeAnalyse::find($id);
        $model->delete();
        return ["message" => "Supprimé avec succès"];
    }
}
