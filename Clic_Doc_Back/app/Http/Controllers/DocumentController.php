<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Services\DocumentGeneratorService;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentGeneratorService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Generate document from frontend form
     */
    public function generate(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'patient_uid' => 'required|string',
                'doctor_id' => 'required|integer',
                'document_type' => 'required|string',
                'form_data' => 'required|array'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document ready for generation'
            ]);

        } catch (\Exception $e) {
            Log::error('Document generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to prepare document'], 500);
        }
    }

    /**
     * View document as HTML with auto-print
     */
    public function view(Request $request, $documentType)
    {
        try {
            $patient = Patient::where('uid', $request->patient_uid)->first();
            $doctor = User::find($request->doctor_id);

            if (!$patient || !$doctor) {
                return response('Patient or Doctor not found', 404);
            }

            // Merge form data from URL parameters
            $formData = $request->except(['patient_uid', 'doctor_id', 'auto_print']);
            $autoPrint = $request->get('auto_print', false);

            $htmlContent = $this->documentService->generateHtmlDocument(
                $documentType,
                $patient,
                $doctor,
                $formData,
                $autoPrint
            );

            return response($htmlContent)->header('Content-Type', 'text/html');

        } catch (\Exception $e) {
            Log::error('Document view error: ' . $e->getMessage());
            return response('Error generating document: ' . $e->getMessage(), 500);
        }
    }

    // Legacy certificate methods for backward compatibility
    public function certificatAptitude($patient_uid, $doctor_id)
    {
        return $this->view(request()->merge([
            'patient_uid' => $patient_uid,
            'doctor_id' => $doctor_id,
            'auto_print' => true
        ]), 'certificat-medical-aptitude-fr');
    }

    public function certificatRepos($patient_uid, $doctor_id)
    {
        return $this->view(request()->merge([
            'patient_uid' => $patient_uid,
            'doctor_id' => $doctor_id,
            'auto_print' => true
        ]), 'certificat-arret-travail');
    }

    public function facturation($patient_uid, $doctor_id)
    {
        return $this->view(request()->merge([
            'patient_uid' => $patient_uid,
            'doctor_id' => $doctor_id,
            'auto_print' => true
        ]), 'facturation');
    }
}