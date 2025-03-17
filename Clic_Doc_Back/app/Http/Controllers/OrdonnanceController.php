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
            'posologie' => 'nullable|string',
            'commentaire' => 'nullable|string',
            'administration_mode' => 'nullable|string',
            'duration_value' => 'nullable|integer',
            'duration_unit' => 'nullable|string',
            'frequency' => 'nullable|string',
            'contraindications' => 'nullable|array', // Accept array
            'matin' => 'nullable|integer',
            'midi' => 'nullable|integer',
            'soir' => 'nullable|integer',
            'au_coucher' => 'nullable|integer',
        ]);

        // Convert contraindications array to a comma-separated string
        $validated['contraindications'] = implode(',', $request->contraindications ?? []);

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
            ->select(
                "m.nom as medicament",
                "ordonnances.*"
            )
            ->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'consultation_id' => 'required|integer',
            'medicament_id' => 'required|integer',
            'posologie' => 'nullable|string',
            'commentaire' => 'nullable|string',
            'administration_mode' => 'nullable|string',
            'duration_value' => 'nullable|integer',
            'duration_unit' => 'nullable|string',
            'frequency' => 'nullable|string',
            'contraindications' => 'nullable|array',
            'matin' => 'nullable|integer',
            'midi' => 'nullable|integer',
            'soir' => 'nullable|integer',
            'au_coucher' => 'nullable|integer',
        ]);

        // Convert contraindications array to a comma-separated string
        $validated['contraindications'] = implode(',', $request->contraindications ?? []);

        $ordonnance = Ordonnance::findOrFail($id);
        $ordonnance->update($validated);

        return response()->json($ordonnance);
    }

    /**
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
     */
    public function imprimer(string $id)
    {
        $data["ordonnance"] = Ordonnance::join("medicaments as m", "m.id", '=', "ordonnances.medicament_id")
            ->where("ordonnances.consultation_id", "=", $id)
            ->select(
                "m.nom as medicament",
                "ordonnances.*"
            )
            ->get();

        $consult = Consultation::find($id);
        $data["patient"] = Patient::find($consult->patient_id);
        $data["docteur"] = User::find($consult->doctor_id);
        $data["entite"] = Entite::find($data["docteur"]->entity_id);

        return view("ordonnance", $data);
    }
}
