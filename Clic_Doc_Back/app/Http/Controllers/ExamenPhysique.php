<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamenPhysique as EP;

class ExamenPhysique extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return EP::find($id);
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
        $ep = EP::find($id);
        $ep->respiratory_system = $request->sys1; 
        $ep->cardiovascular_system = $request->sys2;
        $ep->neurological_system = $request->sys3;
        $ep->musculoskeletal_system = $request->sys4;
        $ep->gastrointestinal_system = $request->sys5;
        $ep->genitourinary_system = $request->sys6;
        $ep->endocrine_system = $request->sys7;
        $ep->lymphatic_system = $request->sys8;
        $ep->hematologic_system = $request->sys9;
        $ep->cutaneous_system = $request->sys10;
        $ep->auditory_system = $request->sys11;
        $ep->visual_system = $request->sys12;
        $ep -> hair = request()->hair;
        $ep -> nails = request()->nails;
        $ep -> face = request()->face;
        $ep -> body = request()->body;
        $ep -> save();

        return $ep;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
