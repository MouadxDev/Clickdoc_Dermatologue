<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PatientMedicalHistory as PMH;
use Illuminate\Http\Request;

class AntMedicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $history = PMH::where("patient_id",'=',$id)->first();
        if($history==null)
        {
            $h = new PMH();
            $h->patient_id = $id;
            $h->chronic = false;
            $h->cardiac = false;
            $h->lung = false;
            $h->kidney = false;
            $h->cancer = false;
            $h->nerves = false;
            $h->gastric = false;
            $h->save();
            return $h;
        }
        return $history;
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
            $h=PMH::find($id);
            $h->chronic = request()->chronic;
            $h->cardiac = request()->cardiac;
            $h->lung = request()->lung;
            $h->kidney = request()->kidney;
            $h->cancer = request()->cancer;
            $h->nerves = request()->nerves;
            $h->gastric = request()->gastric;
            $h->chronic_comment = request()->chronic_comment;
            $h->cardiac_comment = request()->cardiac_comment;
            $h->lung_comment = request()->lung_comment;
            $h->kidney_comment = request()->kidney_comment;
            $h->cancer_comment = request()->cancer_comment;
            $h->nerves_comment = request()->nerves_comment;
            $h->gastric_comment = request()->gastric_comment;
            $h->save();

            return ["message"=>"Antécédents enregistrés"];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
