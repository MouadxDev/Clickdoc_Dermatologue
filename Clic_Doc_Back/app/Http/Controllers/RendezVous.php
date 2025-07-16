<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\RendezVous as ModelsRendezVous;
use App\Models\User;
use App\Models\WaitingList;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RendezVous extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = ModelsRendezVous::where("patient_id","=",request()->patient_id)
            ->where("doctor_id",'=',auth()->user()->id);
        if(request()->has("toGet"))
            return $model->paginate(request()->toGet);
        else
        {
            return $model::all();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doctor = User::where("entity_id",'=',auth()->user()->entity_id)->whereIn("role",["Admin","doctor","docteur"])->first();
		
        if(request()->has("date"))
        {
            return ModelsRendezVous::join("patients as p", "p.id", '=', "rendez_vouses.patient_id")
                ->join("acte_medicals as a", 'a.id', '=', "rendez_vouses.type")
                ->select(
                    "p.name", 
                    "p.surname", 
                    "p.uid", // Fetch the UID from the patients table
                    "rendez_vouses.*", 
                    "a.libelle as type"
                )
                ->whereDate("rendez_vouses.date", Carbon::parse(request()->date)->format('d/m/Y'))
                ->where("rendez_vouses.doctor_id", '=', $doctor->id)
                ->orderBy("rendez_vouses.heure", 'asc')
                ->get();
        }
        
        if(request()->has("month"))
        {
            return ModelsRendezVous::join("patients as p", "p.id", '=', "rendez_vouses.patient_id")
                ->join("acte_medicals as a", 'a.id', '=', "rendez_vouses.type")
                ->select(
                    "p.name", 
                    "p.surname", 
                    "p.uid", // Fetch the UID from the patients table
                    "rendez_vouses.*", 
                    "a.libelle as type"
                )
                ->where("rendez_vouses.doctor_id", '=', $doctor->id)
                ->orderBy("rendez_vouses.heure", 'asc')
                ->get()
                ->filter(function ($item) {
                    $date = explode("/", $item->date);
                    if ($date[1] == request()->month) return $item;
                });
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role == "Admin" || auth()->user()->role == "doctor")
            $doctor = auth()->user();
        else
            $doctor = User::where("entity_id", '=', auth()->user()->entity_id)
                          ->whereIn("role", ["Admin", "doctor"])
                          ->first();
    
        // Vérifie si le patient a déjà un rendez-vous ce jour-là
        $exists = ModelsRendezVous::where('patient_id', $request->patient_id)
            ->where('date', $request->date)
            ->exists();
    
        if ($exists) {
            return response()->json([
                'error' => 'Ce patient a déjà un rendez-vous pour cette date.'
            ], 400);
        }
    
        // Création du nouveau rendez-vous
        $new = new ModelsRendezVous();
        $new->patient_id = $request->patient_id;
        $new->doctor_id = $doctor->id;
        $new->type = $request->type;
        $new->date = $request->date;
        $new->heure = $request->heure;
        $new->statut = $request->statut;
        $new->color = $request->color;
        $new->save();
    
        // Ajout dans la liste d'attente
        $wl = new WaitingList();
        $wl->patient_id = $request->patient_id;
        $wl->type = $request->type;
        $wl->doctor_id = $doctor->id;
        $wl->entity_id = auth()->user()->entity_id;
        $wl->state = "waiting";
        $wl->created_at = Carbon::createFromFormat('d/m/Y', $request->date)->timestamp;
        $wl->save();
    
        return response()->json([
            'success' => 'Le rendez-vous a été créé avec succès.'
        ]);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function is_today() {
		$toSend = [];
        if(request()->has("date"))
        {
            $toSend = ModelsRendezVous::join("patients as p","p.id","=","rendez_vouses.patient_id")->where("rendez_vouses.date","=",request()->date)->where("rendez_vouses.doctor_id",'=',request()->doctor_id || auth()->user()->id)->orderBy("heure",'asc')->select("p.avatar","p.name","p.surname","rendez_vouses.*")->get();
        }
		else {
			$toSend =  ModelsRendezVous::join("patients as p","p.id","=","rendez_vouses.patient_id")->where("rendez_vouses.date","=",date("d/m/Y"))->where("rendez_vouses.doctor_id",'=',request()->doctor_id || auth()->user()->id)->orderBy("heure",'asc')->select("p.avatar","p.name","p.surname","rendez_vouses.*")->get();
		}
        return $toSend;
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
        $rendez_vous = ModelsRendezVous::find($id);
        $oldStat = $rendez_vous->statut;
        $rendez_vous -> statut =request()->statut;
        $rendez_vous->heure = $request->heure ?? '00:00'; 
        $rendez_vous -> save();
        if(request()->statut=="postponed")
        {
            $new = new ModelsRendezVous();
            $new -> patient_id= $rendez_vous->patient_id;
            $new -> doctor_id= $rendez_vous->doctor_id ;
            $new -> type= $rendez_vous->type;
            $new -> date= request()->date;
            $new -> heure= request()->heure ?? '00:00';
            $new -> statut= $oldStat;
            $new -> save() ; 

            if($oldStat=="salle attente")
            {
                $wl = new WaitingList();
                $wl -> patient_id = request()->patient_id;
                $wl -> entity_id = auth()->user()->entity_id;
                $wl -> type = request()->type;
                $wl -> state = "waiting";
                $wl -> created_at = Carbon::createFromFormat('d/m/Y', $request->date)->timestamp;
                $wl -> save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rendezVous = ModelsRendezVous::find($id);
    
        if ($rendezVous) {
            $rendezVous->delete();
            return response()->json(['message' => 'Supprimé avec succès.'], 200);
        }
    
        $waitingList = WaitingList::find($id);
    
        if ($waitingList) {
            $waitingList->delete();
            return response()->json(['message' => 'Patient retiré de la salle d’attente.'], 200);
        }
    
        return response()->json(['message' => 'Aucun rendez-vous ou patient en salle d’attente trouvé.'], 404);
    }
    
    
}