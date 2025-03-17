<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Consultation;
use App\Models\Facture;
use App\Models\Payment;
use App\Models\WaitingList;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /** 
     * Display a listing of the resource.
     */
    public function index()
    {
        // return Payment::join('factures as f','payments.facture_id','=',"f.id")
        // ->join('consultations as c','c.id','=','f.id')
        // ->join('users as u','u.id','=','c.doctor_id')
        // ->where('u.entity_id','=',auth()->user()->entity_id)
        // ->select('f.uid','payments.*')
        // ->paginate(request()->toGet);
        return Payment::join('factures as f', 'payments.facture_id', '=', 'f.id')
            ->join('consultations as c', 'c.id', '=', 'f.consultation_id')
            ->join('users as u', 'u.id', '=', 'c.doctor_id')
            ->where('u.entity_id','=',auth()->user()->entity_id)
            ->select('f.uid','payments.*')  // Select all columns from each table
            ->paginate(request()->toGet);
            }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entrées = Payment::join("factures as f",'f.id','=',"payments.facture_id")
        ->join("consultations as c",'c.id','f.consultation_id')
        ->join("users as u",'u.id','=','c.doctor_id')
        ->where('u.entity_id','=',auth()->user()->entity_id)
        ->select("payments.amount")
        ->get();

        $charges = Charge::join("users as u",'u.id','=',"charges.declarant")
        ->where('u.entity_id','=',auth()->user()->entity_id)
        ->select("charges.*")
        ->get();

        $factures = Facture::join("consultations as c",'c.id','factures.consultation_id')
        ->join("users as u",'u.id','=','c.doctor_id')
        ->where('u.entity_id','=',auth()->user()->entity_id)
        ->select("factures.amount")
        ->get();

        $mca = Facture::join("consultations as c",'c.id','factures.consultation_id')
        ->join("users as u",'u.id','=','c.doctor_id')
        ->where('u.entity_id','=',auth()->user()->entity_id)
        ->whereMonth('factures.created_at', Carbon::now()->month)
        ->select("factures.amount")
        ->get();
        
        $dca = Facture::join("consultations as c",'c.id','factures.consultation_id')
        ->join("users as u",'u.id','=','c.doctor_id')
        ->where('u.entity_id','=',auth()->user()->entity_id)
        ->whereDay('factures.created_at', now()->day)
        ->select("factures.amount")
        ->get();
        
        $wL = WaitingList::where("entity_id",'=',auth()->user()->entity_id)
        ->whereDay('created_at', now()->day)
        ->count();
         
        $tP= Consultation::join('users as u','u.id','=','consultations.doctor_id')
        ->where('u.entity_id','=',auth()->user()->entity_id)
        ->whereDay('consultations.created_at', now()->day)
        ->groupBy("consultations.patient_id")
        ->count();

        return ['charges'=>$charges,'entries'=>$entrées,'chiffre_affaires'=>$factures,'mca'=>$mca,'dca'=>$dca,'wL'=>$wL,'patients'=>$tP];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $payment = new Payment();
        $payment -> patient_id = request() -> patient_id;
        $payment -> facture_id = request() -> facture_id;
        $payment -> amount = request() -> amount ;
        $payment -> type = request() -> type ; 
        $payment -> motif = request() -> motif ; 
        $payment -> save();

        return $payment ;
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
        //
    }
}
