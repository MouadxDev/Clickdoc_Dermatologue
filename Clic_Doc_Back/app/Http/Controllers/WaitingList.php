<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\WaitingList as WL;

class WaitingList extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        $wL = WL::whereDate("waiting_lists.created_at",Carbon::today());
		$wL->where("state","=","waiting")->where("waiting_lists.entity_id",'=',auth()->user()->entity_id)
			->join("acte_medicals as a","a.id",'=','waiting_lists.type')
			-> join("patients as p",'waiting_lists.patient_id','=','p.id')
        	->select("waiting_lists.*","p.name","p.surname","p.avatar","p.uid","a.libelle as type");
        if(request()->has("toGet"))
            return $wL->paginate(request()->toGet);
        return [$wL::all(), $entity_id];
        
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
        $wl = new WL();
        $wl -> patient_id = request()->patient_id;
        $wl -> entity_id = auth()->user()->entity_id;
        $wl -> doctor_id = auth()->user()->doctor_id;
        $wl -> type = request()->type;
        $wl -> state = "waiting";

        $wl -> save();

        return $wl;

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
    public function update(string $id)
    {
        $wL = WL::find($id);
        $wL -> state = request()->status;
        $wL -> doctor_id = auth()->user()->id;
        $wL ->save();

        return $wL ;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function isWaiting()
    {
        if(auth()->user()->role!="Admin")
        return [
            "status"=>false,
        ];

        $wL = WL::where("patient_id","=",request()->patient_id)
        ->where("entity_id",'=',auth()->user()->entity_id)
        ->whereDate("created_at",Carbon::today())
        ->where("state","=","waiting")
        ->first();
        if($wL!=null)
        {
            return [
                "status"=>true,
                "data"=>$wL
            ];
        }
        else
        {
            return [
                "status"=>false,
            ];
        }
    }
}
