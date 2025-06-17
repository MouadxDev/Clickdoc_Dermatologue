<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Diagnostic;
use App\Models\ExamenPhysique;
use App\Models\Facture;
use App\Models\Observations;
use App\Models\Patient;
use App\Models\WaitingList;
//use Carbon\Carbon;

class ConsultationController extends Controller
{
public function index()
{
    $consultation = Consultation::where("doctor_id", auth()->user()->id)
        ->join("patients", "consultations.patient_id", "=", "patients.id")
        ->join("users", "consultations.doctor_id", "=", "users.id");

    if (request()->has("date")) {
        $consultation->whereDate("consultations.created_at", request()->date);
    }

    if (request()->has("patient_id")) {
        $consultation->where("patient_id", request()->patient_id);
    }

    $consultation->select(
        "consultations.*",
        "patients.name",
        "patients.surname",
        "patients.avatar",
        "users.name as docteur"
    );

    $paginated = $consultation->paginate(request()->toGet);

    // Format created_at
    $paginated->getCollection()->transform(function ($item) {
        $item->created_at_formatted = \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i:s');
        return $item;
    });

    return $paginated;
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $consultation = Consultation::where("doctor_id",'=',auth()->user()->id)
        ->where("isFinished","=",0)
        ->orderBy("created_at","desc")
        ->first();

        if($consultation!=null)
        {
            return ["message"=>true,"id"=>$consultation->id];
        }
        else
        {
            return ["message"=>false];
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $consultation = new Consultation();
        $consultation -> motif = request()->motif ; 
        $consultation -> doctor_id = auth()->user()->id;
        $consultation -> patient_id = request()->patient_id;
        $consultation -> wl_id = request()->wl_id;
        $consultation -> isPrivate = true;
        $consultation -> save();

        $consultation -> uid = "C".date("Y")."-".str_pad($consultation->id, 6, '0', STR_PAD_LEFT);;
        $consultation -> save();

        $wl = WaitingList::find(request()->wl_id);
        $wl -> state = "onGoing";
        $wl -> save();

        $examen = new ExamenPhysique();
        $examen -> consultation_id = $consultation->id;
        $examen -> save();

        $diagnostic = new Diagnostic();
        $diagnostic -> consultation_id = $consultation->id;
        $diagnostic -> save();
        
        $observation = new Observations();
        $observation -> consultation_id = $consultation->id;
        $observation -> save();

        $facture = new Facture();
        $facture -> consultation_id = $consultation->id;
        $facture -> uid = "F".date("Y")."-".str_pad($facture->id, 6, '0', STR_PAD_LEFT);;
        $facture -> save();


        return [
            "consultation"=>$consultation->id,
            "uid"=>$consultation->uid,
            "examen"=>$examen -> id,
            "diagnostic"=>$diagnostic ->id ,
            "observation"=>$observation ->id ,
            "facture"=>$facture->id,
            "patient"=>request()->patient_uid 
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $consultation = Consultation::find($id);
        $diagnostic = Diagnostic::where("consultation_id",'=',$id)->first();
        $examen = ExamenPhysique::where("consultation_id",'=',$id)->first();
        $patient = Patient::find($consultation->patient_id);
        $facture = Facture::where("consultation_id",'=',$id)->first() ;
        $observation = Observations::where("consultation_id",'=',$id)->first() ;
        if($facture == null)
        {
            $facture_id = null;
        }
        else 
        {
            $facture_id = $facture->id;
        }

        return [
            "consultation"=>$consultation->id,
            "deets"=>$consultation,
            "uid"=>$consultation->uid,
            "examen"=>$examen -> id,
            "diagnostic"=>$diagnostic ->id ,
            "patient"=>$patient->uid,
            "observation"=>$observation->id,
            "facture"=>$facture_id
        ];
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
        $c = Consultation::find($id);
        $c -> isPrivate = request()->isPrivate;
		if(request()->has("motif"))
		{	
        	$c -> motif = request()->motif;
        	$c -> isFinished = request()->isFinished;
		}
		else 
        	$c -> isFinished = 1;
		$c -> save();



        return ["message"=>"Enregistré avec succès"] ;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
	
	public function cnss(string $id)
	{
		$consultation = Consultation::find($id);
		$patient = Patient::find($consultation->patient_id);
		$facture = Facture::where('consultation_id','=',$id)->first();
		
		return view('cnss',['consultaion'=>$consultation,'patient'=>$patient,'facture'=>$facture]);
	}
}
