<?php

namespace App\Http\Controllers;

use App\Models\Dosage;
use App\Models\Medicament;
use Illuminate\Http\Request;

class MedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Medicament::join("laboratoires as l" , "l.id","=","medicaments.lab_id")
        
        ->select('l.name as lab',"medicaments.*");

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
        $medicament = new Medicament();
        $medicament -> nom = request()->nom;
        $medicament -> lab_id = request()->lab_id;
        $medicament -> prix = request()->prix;
        $medicament -> save();

        return $medicament;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Dosage::where("medicament_id","=",$id)->get();
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
        //
    }
}
