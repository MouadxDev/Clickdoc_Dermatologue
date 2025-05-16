<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FichePatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::get(); // no arrow after Patient::
        return response()->json($patients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'avatar' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);

        $patient = Patient::create($validated);
        return response()->json($patient, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Get basic patient information
        $patient = Patient::findOrFail($id);
    
        // Get total consultations count for this patient
        $totalConsultations = DB::table('consultations')
            ->where('patient_id', $id)
            ->count();
    
        // Pagination parameters
        $perPage = 10; // you can change this
        $page = request()->get('page', 1);
    
        // Step 1: Get consultations paginated for the patient, include created_at
        $consultationsQuery = DB::table('consultations as c')
            ->select('c.id as consultation_id', 'c.patient_id', 'c.motif', 'c.created_at')
            ->where('c.patient_id', $id);
    
        // Get paginated consultations (using skip/take because Query Builder paginate() returns full paginator with URLs)
        $consultations = $consultationsQuery
            ->orderBy('c.created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
    
        // Extract consultation IDs for current page
        $consultationIds = $consultations->pluck('consultation_id')->toArray();
    
        // Step 2: Get analyses related to those consultations
        $analyses = DB::table('demande_analyses as da')
            ->join('analyses as a', 'da.analyse_id', '=', 'a.id')
            ->select('da.consultation_id', 'a.libelle as analyse_name')
            ->whereIn('da.consultation_id', $consultationIds)
            ->get()
            ->groupBy('consultation_id');
    
        // Step 3: Get medications related to those consultations
        $medications = DB::table('ordonnances as o')
            ->join('medicaments as m', 'o.medicament_id', '=', 'm.id')
            ->select('o.consultation_id', 'm.nom as medicament_name')
            ->whereIn('o.consultation_id', $consultationIds)
            ->get()
            ->groupBy('consultation_id');
    
        // Build combined result for each consultation
        $result = [];
    
        foreach ($consultations as $consultation) {
            $cId = $consultation->consultation_id;
    
            $result[] = [
                'consultation_id' => $cId,
                'patient_id' => $consultation->patient_id,
                'motif' => $consultation->motif,
                'created_at' => $consultation->created_at,  // <-- added here
                'analyses' => isset($analyses[$cId]) ? $analyses[$cId]->pluck('analyse_name')->toArray() : [],
                'medications' => isset($medications[$cId]) ? $medications[$cId]->pluck('medicament_name')->toArray() : [],
            ];
        }
    
        // Return patient info with consultations (paginated) and total count
        return response()->json([
            'patient' => $patient,
            'total_consultations' => $totalConsultations,
            'page' => (int)$page,
            'per_page' => $perPage,
            'consultations' => $result,
        ]);
    }
    
    
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'avatar' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update($validated);
        return response()->json($patient);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        return response()->json(['message' => 'Patient deleted successfully']);
    }

    /**
     * Get all consultations for a specific patient
     */
    public function getConsultations(string $id)
    {
        $consultations = DB::table('consultations as c')
            ->select([
                'c.id as consultation_id',
                'c.patient_id',
                'c.motif',
                'c.created_at',
                'c.updated_at',
                DB::raw('GROUP_CONCAT(DISTINCT a.libelle) as analyses'),
                DB::raw('GROUP_CONCAT(DISTINCT m.nom) as medicaments')
            ])
            ->leftJoin('demande_analyses as da', 'c.id', '=', 'da.consultation_id')
            ->leftJoin('analyses as a', 'da.analyse_id', '=', 'a.id')
            ->leftJoin('ordonnances as o', 'c.id', '=', 'o.consultation_id')
            ->leftJoin('medicaments as m', 'o.medicament_id', '=', 'm.id')
            ->where('c.patient_id', $id)
            ->groupBy('c.id', 'c.patient_id', 'c.motif', 'c.created_at', 'c.updated_at')
            ->orderBy('c.created_at', 'desc')
            ->get();

        return response()->json($consultations);
    }

    /**
     * Get all analyses for a specific patient
     */
    public function getAnalyses(string $id)
    {
        $analyses = DB::table('consultations as c')
            ->select([
                'a.id as analyse_id',
                'a.libelle as analyse_name',
                'c.id as consultation_id',
                'c.created_at as consultation_date'
            ])
            ->join('demande_analyses as da', 'c.id', '=', 'da.consultation_id')
            ->join('analyses as a', 'da.analyse_id', '=', 'a.id')
            ->where('c.patient_id', $id)
            ->orderBy('c.created_at', 'desc')
            ->get();

        return response()->json($analyses);
    }

    /**
     * Get all medications for a specific patient
     */
    public function getMedicaments(string $id)
    {
        $medicaments = DB::table('consultations as c')
            ->select([
                'm.id as medicament_id',
                'm.nom as medicament_name',
                'c.id as consultation_id',
                'c.created_at as consultation_date',
                'o.administration_mode',
                'o.duration_value',
                'o.duration_unit'
            ])
            ->join('ordonnances as o', 'c.id', '=', 'o.consultation_id')
            ->join('medicaments as m', 'o.medicament_id', '=', 'm.id')
            ->where('c.patient_id', $id)
            ->orderBy('c.created_at', 'desc')
            ->get();

        return response()->json($medicaments);
    }
} 