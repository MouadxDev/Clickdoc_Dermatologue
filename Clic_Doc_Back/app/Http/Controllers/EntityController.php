<?php

namespace App\Http\Controllers;

use App\Models\Entite;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = new Entite();
        if(request()->has("toGet")) 
            return $model->paginate(request()->toGet);
        else 
            return $model->get();
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
        $entite = new Entite();
        $entite -> licence = false;
        $entite ->  creation = date("d/m/Y");
        $entite -> renewal = 0;
        /* 1- monthly /   / 2-yearly /   / 3- bi-yearly   */
        $entite -> payment_cycle = 0 ;
        $entite -> logo  = request()->logo;
        $entite -> name  = request()->name;
        $entite -> city  = request()->city;
        $entite -> adress  = request()->adress;
        $entite -> contact_email  = request()->contact_email;
        $entite -> contact_name  = request()->contact_name;
        $entite -> used_trial  = 0;
        $entite -> justification  = request()->justification;
        $entite -> save();

        return $entite;

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Entite::find($id);
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
        $entite = Entite::find($id);
        $entite -> licence = request()->licence;
        $entite -> expiration = request()->expiration;
        $entite -> renewal = request()->renewal;
        /* 1- monthly /   / 2-yearly /   / 3- bi-yearly   */
        $entite -> payment_cycle = request()->payment_cycle ;
        $entite -> logo  = request()->logo;
        $entite -> name  = request()->name;
        $entite -> city  = request()->city;
        $entite -> adress  = request()->adress;
        $entite -> contact_email  = request()->contact_email;
        $entite -> contact_name  = request()->contact_name;
        $entite -> type  = request()->type;
        $entite -> used_trial  = 0;
        $entite -> justification  = request()->justification;
        $entite -> save();

        return ["message"=>"Licence modifiée"];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
