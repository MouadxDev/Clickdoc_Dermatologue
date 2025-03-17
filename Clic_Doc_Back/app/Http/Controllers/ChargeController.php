<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Charge::join("users as u",'u.id','=','charges.declarant',)
        ->where("u.entity_id","=",auth()->user()->entity_id)
        ->select("charges.*","u.name as declarant")
        ->paginate(request()->toGet);
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
        $new = new Charge();
        $new -> entity_id = auth()->user()->entity_id;
        $new -> declarant = auth()->user()->id;
        $new -> montant = request()->montant;
        $new -> motif = request()->motif;
        $new -> save();

        return $new ; 

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
        $charge = Charge::find($id);
        $charge -> delete();

        return ["message",'=',"Supprimé avec succès"];
    }
}
