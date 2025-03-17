<?php

namespace App\Http\Controllers;

use App\Models\ActeMedical;
use App\Models\ArticleFacture;
use App\Models\Consultation;
use App\Models\Facture;
use App\Models\Soin;
use App\Models\SoinPerformed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SoinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->role=="Admin" OR auth()->user()->role=="doctor")
            $doctor = auth()->user() ;
        else 
            $doctor = User::where("entity_id",'=',auth()->user()->entity_id)->whereIn("role",["Admin","doctor"]) -> first();
        $soins =  Soin::join("acte_medicals as a","soins.acte_id",'=',"a.id")
        ->join("consultations as c","c.id",'=',"soins.consultation_id")
        ->where("doctor_id",'=',$doctor->id)
        ->where("patient_id",'=',request()->patient_id)
        ->select("a.prix as prix","a.libelle","soins.*","c.doctor_id","c.patient_id")
        -> get();

        for($i=0;$i<count($soins);$i++)
        {
            $performed = SoinPerformed::where("soin_id",'=',$soins[$i]->id)->get();
            $soins[$i]["nbr_performed"] = $performed;
        }

        return $soins;

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
        $soin = new Soin();
        $soin -> consultation_id = request()->consultation_id;
        $soin -> acte_id = request()->acte_id;
        $soin -> nbr_sceances = request()->nbr_sceances;
        $soin -> save();

        return $soin;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $soins =  Soin::join("acte_medicals as a","soins.acte_id",'=',"a.id")
        ->where("soins.consultation_id",'=',$id)
        ->select("a.libelle as soin","soins.*")
        -> get();
        for($i=0;$i<count($soins);$i++)
        {
            $performed = SoinPerformed::where("soin_id",'=',$soins[$i]->id)->get();
            $soins[$i]["nbr_performed"] = $performed;
        }

        return $soins;
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $c = Consultation::find($id);
        $d = User::find($c->doctor_id);

        $doctor_fee = $d -> fee;

        $soins =  Soin::join("acte_medicals as a","soins.acte_id",'=',"a.id")
        ->where("soins.consultation_id",'=',$id)
        ->select("a.prix as prix","soins.*")
        -> get();

        for($i=0;$i<count($soins);$i++)
        {
            $performed = SoinPerformed::where("soin_id",'=',$soins[$i]->id)->count();
            $soins[$i]["montant"] = $performed * $soins[$i]->prix;
        }

        return ["doctor_fee"=>$doctor_fee , "liste"=>$soins];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $soin = Soin::find($id);
        $acte = ActeMedical::find($soin->acte_id);

        $s = new SoinPerformed();
        $s -> soin_id = $id;
        $s -> consultation_id = $soin->consultation_id;
        $s -> save();

        $f = new Facture();
        $f -> consultation_id  = $soin->consultation_id;
        $f -> amount = 0;
        $f -> save();

        $a = new ArticleFacture();
        $a -> facture_id = $f->id;
        $a -> libelle = 'scéance de '.$acte->libelle;
        $a -> prix = request()->prix;
        $a -> type = 3 ;
        $a -> save();


        $f -> uid = "F".date("Y")."-".str_pad($f->id, 6, '0', STR_PAD_LEFT);;
        $f->amount = $a->prix;
        $f->save();

        return $s;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $soin = Soin::find($id);
        $soin -> delete();

        return ["message"=>"Supression avec succès"];
    }
}
