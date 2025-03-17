<?php

namespace App\Http\Controllers;

use App\Models\ArticleFacture;
use App\Models\Consultation;
use App\Models\Facture;
use App\Models\Payment;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $factures = Facture::join("consultations as c","c.id","=","factures.consultation_id")
        ->where("c.patient_id","=",request()->patient_id)
        ->where("c.doctor_id","=",auth()->user()->id)
        ->get();

        $paiements = Payment::join("factures as f",'f.id','=','payments.facture_id')->where("patient_id",'=',request()->patient_id)->select('f.uid','payments.*') ->get();

        $amount = 0;
        $paid = 0;

        for($i=0;$i<count($factures);$i++)
        {
            $amount += $factures[$i]->amount;
        }
        for($i=0;$i<count($paiements);$i++)
        {
            $paid += $paiements[$i]->amount;
        }

        return [
            "amount"=>$amount,
            "paid"=>$paid,
            "left"=>$amount-$paid,
            "paiements"=>$paiements
        ];
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
        $soin = new ArticleFacture();
        $soin -> facture_id = request() -> facture_id;
        $soin -> libelle = request()->libelle;
        $soin -> prix = request()->prix;
        $soin -> type = request()->type ;
        $soin -> save();
        return $soin;
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
        $soin = ArticleFacture::find($id);
        $soin -> facture_id = request() -> facture_id;
        $soin -> libelle = request()->libelle;
        $soin -> prix = request()->prix;
        $soin -> type = request()->type ;
        $soin -> save();

        return $soin;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $soin = ArticleFacture::find($id);
        $soin ->delete();

        return $soin;
    }
}
