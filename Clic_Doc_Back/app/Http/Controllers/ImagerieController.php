<?php

namespace App\Http\Controllers;

use App\Models\Imagerie;
use Illuminate\Http\Request;

class ImagerieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Imagerie::where("patient_id",'=',request()->patient_id)->orderBy('id','desc')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $imagerie = new Imagerie();
        $imagerie -> doctor_id = auth()->user()->id;
        $imagerie -> patient_id = request()->patient_id;
        $imagerie -> image = request()->image;
        $imagerie -> commentaire = request()->comment;
        $imagerie -> save();

        return $imagerie;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Imagerie::where("patient_id",'=',$id)->get();
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
        $imagerie = Imagerie::find($id);
        $imagerie -> delete();

        return ["message"=>"Supprimé avec succès"];
    }
}
