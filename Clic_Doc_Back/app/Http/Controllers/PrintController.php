<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Entite;
use App\Models\User;
use App\Models\ArticleFacture; 
use App\Models\Facture; 
use Illuminate\Support\Facades\DB;

class PrintController extends Controller
{

	public function generateRecu($payment_id, $doctor_id)
	{
		// Fetch doctor details
		$doctor = DB::table('users')->where('id', $doctor_id)->first();
		if (!$doctor) {
			return abort(404, 'Doctor not found');
		}
	
		// Fetch the entity related to the doctor
		$entity = DB::table('entites')->where('id', $doctor->entity_id)->first();
		if (!$entity) {
			return abort(404, 'Entity not found');
		}
	
		// Fetch payment details from the payments table
		$payment = DB::table('payments')->where('id', $payment_id)->first();
		if (!$payment) {
			return abort(404, 'Payment not found');
		}
	
		// Fetch patient name using the patient_id from the payment table
		$patient = DB::table('patients')->where('id', $payment->patient_id)->first();
		if (!$patient) {
			return abort(404, 'Patient not found');
		}
	
		// Data to pass to the view
		$data = [
			'doctor' => $doctor->name,
			'patient' => "{$patient->name} {$patient->surname}",
			'payment_value' => $payment->amount,
			'payment_date' => $payment->created_at,
			'entity' => [
				'name' => $entity->name,
				'address' => $entity->adress,
				'city' => $entity->city,
				'email' => $entity->contact_email,
				'logo' => $entity->logo, // Optional, can be null
			],
			'date' => now()->format('d/m/Y H:i'),
		];
	
		// Return the receipt view
		return view('recu', $data);
	}
	
	
	



	public function certificat($type, $id, $docteur)
	{
		$patient = Patient::where('uid', '=', $id)->first();
		$user = User::find($docteur);
	
		if ($user->role == 'Admin') {
			$docteur = $user;
		} else {
			$docteur = User::where('entity_id', '=', $user->entity_id)->where('role', '=', 'doctor')->first();
		}
	
		$entite = Entite::find($docteur->entity_id);
	
		// ✅ Retrieve branding file (same as imprimer)
		$branding = DB::table('entity_branding')
			->where('entity_id', $entite->id)
			->select('file_path')
			->first();
	
		$branding_file = $branding?->file_path;
	
		// ✅ Add to all view returns
		$data = [
			'patient' => $patient,
			'docteur' => $docteur,
			'entite' => $entite,
			'branding_file' => $branding_file
		];
	
		if ($type == 'aptitude') {
			return view('certificat-aptitude', $data);
		}
		if ($type == 'repos') {
			return view('certificat-medical', $data);
		}
		if ($type == 'maladpro') {
			return view('certificat-maladie-pro', $data);
		}
	
		// Optional: handle unknown type
		abort(404, "Certificat type inconnu.");
	}
	

	

	public function facturation($uid, $docteurId)
	{
		// Fetch the patient by UID
		$patient = Patient::where('uid', '=', $uid)->first();
	
		if (!$patient) {
			return view('facture-selection', [
				'message' => 'Erreur : Patient introuvable. Veuillez vérifier le UID fourni.',
				'factures' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10), // Empty pagination instance
			]);
		}
	
		$patientId = $patient->id;
	
		// Fetch the doctor
		$user = User::find($docteurId);
	
		if (!$user) {
			return view('facture-selection', [
				'message' => 'Erreur : Docteur introuvable. Veuillez vérifier l\'identifiant fourni.',
				'factures' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
			]);
		}
	
		if ($user->role == 'Admin') {
			$docteur = $user;
		} else {
			$docteur = User::where('entity_id', '=', $user->entity_id)
				->where('role', '=', 'doctor')
				->first();
	
			if (!$docteur) {
				return view('facture-selection', [
					'message' => 'Erreur : Aucun docteur associé trouvé.',
					'factures' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
				]);
			}
		}
	
		// Fetch unpaid invoices for the patient with pagination
		$factures = Facture::join('consultations', 'factures.consultation_id', '=', 'consultations.id')
			->where('consultations.patient_id', '=', $patientId)
			// ->where('factures.statut', '=', 'Payé en totalité')
			->where('factures.amount', '>', 0)
			->select('factures.*')
			->paginate(10); // Use paginate()
	
		return view('facture-selection', [
			'factures' => $factures,
			// 'message' => $factures->isEmpty() ? 'Aucun facture impayée disponible pour ce patient.' : null,
			'message' => $factures->isEmpty() ? 'Aucun facture disponible pour ce patient.' : null,
			
		]);
	}
	
	
	
	public function printFacture($factureId, Request $request)
	{
		// Fetch UID and Doctor ID from the request
		$uid = $request->query('uid');
		$docteurId = $request->query('docteur');
	
		// Debugging the parameters
		if (!$uid || !$docteurId) {
			return "Missing parameters: UID or Doctor ID.";
		}
	
		// Proceed with fetching patient and doctor details
		$patient = Patient::where('uid', '=', $uid)->first();
		if (!$patient) {
			return "Erreur : Patient introuvable.";
		}
	
		$docteur = User::find($docteurId);
		if (!$docteur) {
			return "Erreur : Docteur introuvable.";
		}
	
		$facture = Facture::find($factureId);
		if (!$facture) {
			return "Erreur : Facture introuvable.";
		}
	
		$entite = Entite::find($docteur->entity_id);
		$articles = ArticleFacture::where('facture_id', '=', $factureId)->get();
		$articlesGrouped = $articles->groupBy('type');
		
		return view('facture', [
			'patient' => $patient,
			'docteur' => $docteur,
			'entite' => $entite,
			'facture' => $facture,
			'articlesGrouped' => $articlesGrouped,
		]);
	}

}
