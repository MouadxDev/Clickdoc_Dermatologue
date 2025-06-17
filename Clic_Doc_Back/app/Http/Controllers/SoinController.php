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
     * Optional filter: ?patient_id=1
     */
    public function index()
    {
        // If patient_id is provided, filter only by patient_id
        if (request()->has('patient_id')) {
            $patientId = request()->patient_id;
    
            $soins = Soin::join('acte_medicals as a', 'soins.acte_id', '=', 'a.id')
                ->join('consultations as c', 'c.id', '=', 'soins.consultation_id')
                ->where('c.patient_id', $patientId)
                ->select('a.prix as prix', 'a.libelle', 'soins.*', 'c.doctor_id', 'c.patient_id')
                ->get();
    
            foreach ($soins as $soin) {
                $soin->nbr_performed = SoinPerformed::where('soin_id', $soin->id)->count();
            }
    
            return response()->json($soins);
        }
    
        // Else, default behavior: filter by doctor associated with logged-in user
        $user = auth()->user();
    
        if ($user->role === 'Admin' || $user->role === 'doctor') {
            $doctor = $user;
        } else {
            $doctor = User::where('entity_id', $user->entity_id)
                ->whereIn('role', ['Admin', 'doctor'])
                ->first();
    
            if (!$doctor) {
                return response()->json(['error' => 'No doctor found for your entity.'], 403);
            }
        }
    
        $soins = Soin::join('acte_medicals as a', 'soins.acte_id', '=', 'a.id')
            ->join('consultations as c', 'c.id', '=', 'soins.consultation_id')
            ->where('c.doctor_id', $doctor->id)
            ->select('a.prix as prix', 'a.libelle', 'soins.*', 'c.doctor_id', 'c.patient_id')
            ->get();
    
        foreach ($soins as $soin) {
            $soin->nbr_performed = SoinPerformed::where('soin_id', $soin->id)->count();
        }
    
        return response()->json($soins);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $soin = new Soin();
        $soin->consultation_id = $request->consultation_id;
        $soin->acte_id = $request->acte_id;
        $soin->nbr_sceances = $request->nbr_sceances;
        $soin->patient_id = $request->patient_id;
        $soin->save();
    
        return response()->json($soin);
    }
    
    

    /**
     * Display soins for a specific consultation.
     */
    public function show(string $id)
    {
        $soins = Soin::join('acte_medicals as a', 'soins.acte_id', '=', 'a.id')
            ->where('soins.consultation_id', $id)
            ->select('a.libelle as soin', 'soins.*')
            ->get();

        foreach ($soins as $soin) {
            $soin->nbr_performed = SoinPerformed::where('soin_id', $soin->id)->get();
        }

        return response()->json($soins);
    }

    /**
     * Display facture data for a consultation.
     */
    public function edit(string $id)
    {
        $consultation = Consultation::findOrFail($id);
        $doctor = User::findOrFail($consultation->doctor_id);

        $soins = Soin::join('acte_medicals as a', 'soins.acte_id', '=', 'a.id')
            ->where('soins.consultation_id', $id)
            ->select('a.prix as prix', 'soins.*')
            ->get();

        foreach ($soins as $soin) {
            $performedCount = SoinPerformed::where('soin_id', $soin->id)->count();
            $soin->montant = $performedCount * $soin->prix;
        }

        return response()->json([
            'doctor_fee' => $doctor->fee,
            'liste' => $soins,
        ]);
    }

    /**
     * Record a new performed soin and create a facture.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'prix' => 'numeric|min:0',
        ]);

        $soin = Soin::findOrFail($id);
        $acte = ActeMedical::findOrFail($soin->acte_id);

        $s = new SoinPerformed();
        $s->soin_id = $id;
        $s->consultation_id = $soin->consultation_id;
        $s->save();

        $f = new Facture();
        $f->consultation_id = $soin->consultation_id;
        $f->amount = 0;
        $f->save();

        $a = new ArticleFacture();
        $a->facture_id = $f->id;
        $a->libelle = 'scéance de ' . $acte->libelle;
        $a->prix = $request->prix;
        $a->type = 3;
        $a->save();

        $f->uid = "F" . date("Y") . "-" . str_pad($f->id, 6, '0', STR_PAD_LEFT);
        $f->amount = $a->prix;
        $f->save();

        return response()->json($s);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $soin = Soin::findOrFail($id);
        $soin->delete();

        return response()->json(['message' => 'Suppression avec succès']);
    }

}
