<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Entite;
use App\Models\Ordonnance;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdonnanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Ordonnance::join("consultations as c", "c.id", '=', "ordonnances.consultation_id")
            ->groupBy("ordonnances.consultation_id")
            ->join("patients as p", "p.id", '=', "c.patient_id")
            ->select("p.name", "p.surname", "c.uid", "p.avatar", "c.doctor_id", "c.motif", DB::raw("count(*) as medocs"))
            ->where("c.doctor_id", "=", auth()->user()->id);

        if (request()->has("patient_id")) {
            $model->where("c.patient_id", "=", request()->patient_id);
        }

        if (request()->has("toGet")) {
            return $model->paginate(request()->toGet);
        } else {
            return $model->get();
        }
    }

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
     {
         $validated = $request->validate([
             'consultation_id' => 'required|integer',
             'medicament_id' => 'required|integer',
             'commentaire' => 'nullable|string',
             'administration_mode' => 'nullable|string',
             'duration_value' => 'nullable|integer',
             'duration_unit' => 'nullable|string',
             'frequency' => 'nullable|array',
             'contraindications' => 'nullable|array',
             'matin' => 'nullable|integer',
             'midi' => 'nullable|integer',
             'soir' => 'nullable|integer',
             'au_coucher' => 'nullable|integer',
             'treatment_context' => 'nullable|string',
             'application_site' => 'nullable|string',
             'special_instructions' => 'nullable|string',
         ]);
     
         // Convert array fields to JSON strings
         $validated['commentaire'] = json_encode($request->commentaire ?? []);
         $validated['frequency'] = json_encode($request->frequency ?? []);
         $validated['contraindications'] = json_encode($request->contraindications ?? []);
     
         $ordonnance = Ordonnance::create($validated);
     
         return response()->json($ordonnance, 201);
     }
     

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Ordonnance::join("medicaments as m", "m.id", '=', "ordonnances.medicament_id")
            ->where("ordonnances.consultation_id", "=", $id)
            ->select("m.nom as medicament", "ordonnances.*")
            ->get()
            ->map(function ($ordonnance) {
                $ordonnance->commentaire = json_decode($ordonnance->commentaire, true);
                $ordonnance->contraindications = json_decode($ordonnance->contraindications, true);
                return $ordonnance;
            });
    }
    

    /**
     * Update the specified resource in storage.
    */

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'consultation_id' => 'required|integer',
            'medicament_id' => 'required|integer',
            'commentaire' => 'nullable|string',
            'administration_mode' => 'nullable|string',
            'duration_value' => 'nullable|integer',
            'duration_unit' => 'nullable|string',
            'frequency' => 'nullable|array',
            'contraindications' => 'nullable|array',
            'matin' => 'nullable|integer',
            'midi' => 'nullable|integer',
            'soir' => 'nullable|integer',
            'au_coucher' => 'nullable|integer',
            'treatment_context' => 'nullable|string',
            'application_site' => 'nullable|string',
            'special_instructions' => 'nullable|string',
        ]);
    
        // Convert array fields to JSON strings
        $validated['commentaire'] = json_encode($request->commentaire ?? []);
        $validated['frequency'] = json_encode($request->frequency ?? []);
        $validated['contraindications'] = json_encode($request->contraindications ?? []);
    
        $ordonnance = Ordonnance::findOrFail($id);
        $ordonnance->update($validated);
    
        return response()->json($ordonnance);
    }
    

    /*
     * Remove the specified resource from storage.
    */

    public function destroy(string $id)
    {
        $ordonnance = Ordonnance::find($id);
        $ordonnance->delete();

        return response()->json(['message' => 'Supression avec succès']);
    }

    /**
     * Print the specified ordonnance resource.
    **/
    
    public function imprimer(string $id)
    {
        // Get ordonnance data with medication names
        $data["ordonnance"] = Ordonnance::join("medicaments as m", "m.id", '=', "ordonnances.medicament_id")
            ->where("ordonnances.consultation_id", "=", $id)
            ->select(
                "m.nom as medicament",
                "ordonnances.*"
            )
            ->get();
    
        // Get the consultation
        $consult = Consultation::find($id);
    
        // Get related patient and doctor
        $data["patient"] = Patient::find($consult->patient_id);
        $data["docteur"] = User::find($consult->doctor_id);
    
        // Get entity
        $data["entite"] = Entite::find($data["docteur"]->entity_id);
    
        // Get branding file path using entity_id
        $branding = DB::table('entity_branding')
            ->where('entity_id', $data["entite"]->id)
            ->select('file_path')
            ->first();
    
        // Add branding file path to data
        $data["branding_file"] = $branding?->file_path;
    
        return view("ordonnance", $data);
    }
}
