<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientAllergyHistory as PAH;

class AntAllergies extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = PAH::where("patient_id","=",request()->patient_id);
        if(request()->has("toGet"))
            return $model->paginate(request()->toGet);
        else
        {
            return $model::all();
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
        $h = new PAH();
        $h -> patient_id = request()->patient_id;
        $h -> allergy = request()->allergy;
        $h -> reaction = request()->reaction;
        $h -> save();

        return ["message"=>"Ajouté avec succès"];
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
        $sh = PAH::find($id);
        $sh -> delete();

        return ["message"=>"Supprimé avec succès"];
    }
}
