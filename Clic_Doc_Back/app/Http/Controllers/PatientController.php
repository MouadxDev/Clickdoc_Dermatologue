<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = new Patient;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     
    public function store_old(Request $request)
    {
        $patient = new Patient();
        $patient -> sex = request()-> sex;
        $patient -> name = request()-> name;
        $patient -> avatar = request()-> avatar;
        $patient -> surname = request()-> surname;
        $patient -> date_of_birth = request()-> date_of_birth;
        $patient -> phone = request() -> phone ;
        $patient -> CIN = request() -> CIN ;
        $patient -> diabetes = request() -> diabetes ;
        $patient -> blood_type = request() -> blood_type ;
        $patient -> coverage = request() -> coverage ;
        $patient -> coverage_type = request() -> coverage_type ;
		$patient -> coverage_number = request() -> coverage_number ;
        $patient -> save();

        $patient -> uid = "P".date("Y")."-".str_pad($patient->id, 6, '0', STR_PAD_LEFT);;
        $patient -> save();

        return $patient;
    }
    */

    public function store(Request $request)
    {
        // Validate only the last two fields, allowing them to be null or a string
        $request->validate([
            'coverage_type' => 'nullable|string', // Allows null or string
            'coverage_number' => 'nullable|string' // Allows null or string
        ]);
    
        // Create a new Patient instance and assign values from the request without validating other fields
        $patient = new Patient();
        $patient->sex = $request->sex;
        $patient->name = $request->name;
        $patient->avatar = $request->avatar;
        $patient->surname = $request->surname;
        $patient->date_of_birth = $request->date_of_birth;
        $patient->phone = $request->phone;
        $patient->CIN = $request->CIN;
        $patient->diabetes = $request->diabetes;
        $patient->blood_type = $request->blood_type;
        $patient->coverage = $request->coverage;
    
        // Set default values for optional fields if they are not provided
        $patient->coverage_type = $request->coverage_type ?? 'N/A';
        $patient->coverage_number = $request->coverage_number ?? 'N/A';
    
        // Save the patient record to the database
        $patient->save();
    
        // Generate and assign a unique UID after saving to get the patient ID
        $patient->uid = "P" . date("Y") . "-" . str_pad($patient->id, 6, '0', STR_PAD_LEFT);
        $patient->save();
    
        // Return the newly created patient record
        return $patient;
    }
    
    public function displayPatients(Request $request)
    {
        // Retrieve query parameters
        $uid = $request->input('uid');
        $sex = $request->input('sex');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
    
        // Build the query dynamically
        $query = Patient::query();
    
        if ($uid) {
            $query->where('uid', 'like', "%{$uid}%");
        }
    
        if ($sex) {
            $query->where('sex', $sex);
        }
    
        if ($start_date) {
            $query->whereRaw(
                "STR_TO_DATE(date_of_birth, '%d/%m/%Y') >= ?",
                [$start_date]
            );
        }
    
        if ($end_date) {
            $query->whereRaw(
                "STR_TO_DATE(date_of_birth, '%d/%m/%Y') <= ?",
                [$end_date]
            );
        }
    
        // Paginate results (10 per page)
        $patients = $query->paginate(10);
    
        // Pass current filters back to the view
        return view('patient_list', compact('patients', 'uid', 'sex', 'start_date', 'end_date'));
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Patient::where("uid","=",$id)->first();
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
        $patient = Patient::find($id);
        $patient -> sex = request()-> sex;
        $patient -> name = request()-> name;
        $patient -> avatar = request()-> avatar;
        $patient -> surname = request()-> surname;
        $patient -> date_of_birth = request()-> date_of_birth;
        $patient -> phone = request() -> phone ;
        $patient -> CIN = request() -> CIN ;
        $patient -> diabetes = request() -> diabetes ;
        $patient -> blood_type = request() -> blood_type ;
        $patient -> coverage = request() -> coverage ;
        $patient -> coverage_type = request() -> coverage_type ;
		$patient -> coverage_number = request() -> coverage_number ;
        $patient -> observation = request() -> observation ;
        $patient -> save();

        return ["message"=>"Modifié avec succès"];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(string $filter)
    {

        return Patient::where("CIN","LIKE",$filter."%")->orWhere("phone","LIKE",$filter."%")->get()->take(3);
    }
}
