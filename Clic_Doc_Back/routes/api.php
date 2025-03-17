<?php

use App\Http\Controllers\ActeMedicalController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AnalyseController;
use App\Http\Controllers\AntDrugs;
use App\Http\Controllers\AntAllergies;
use App\Http\Controllers\AntFamiliaux;
use App\Http\Controllers\AntChirurgicaux;
use App\Http\Controllers\AntMedicController;
use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FunctionalitiesController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\WaitingList;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DemandeAnalyseController;
use App\Http\Controllers\Diagnostic;
use App\Http\Controllers\DoseController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\ExamenPhysique;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ImagerieController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\MesureController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\OrdonnanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RendezVous;
use App\Http\Controllers\SoinController;
use App\Http\Controllers\TableauPersonaliseController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\StockController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix("/initiate")->group(function(){
    Route::post("/request",[FunctionalitiesController::class, "isInitiated"]);
    Route::post("/go",[FunctionalitiesController::class, "initiate"]);
});

Route::prefix("/auth")->group(function(){
    Route::post("/login",[AuthenticationController::class, "login"]);
    Route::middleware('auth:sanctum')->post("/logout",[AuthenticationController::class, "logout"]);
});

Route::prefix("/v1")->middleware('auth:sanctum')->group(function (){
    Route::resource("/patient",PatientController::class);
    Route::resource("/patient_ant_medicaux",AntMedicController::class);
    Route::resource("/patient_ant_chirurgicaux",AntChirurgicaux::class);
    Route::resource("/patient_ant_familiaux",AntFamiliaux::class);
    Route::resource("/patient_ant_allergies",AntAllergies::class);
    Route::resource("/patient_ant_drugs",AntDrugs::class);
    Route::resource("/upload",UploadController::class);
    Route::resource("/waiting-list",WaitingList::class);
    Route::resource("/consultation",ConsultationController::class);
    Route::resource("/examen-physique",ExamenPhysique::class);
    Route::resource("/diagnostic",Diagnostic::class);
    Route::resource("/observation",ObservationController::class);
    Route::resource("/medicament",MedicamentController::class);
    Route::resource("/doses",DoseController::class);
    Route::resource("/ordonnance",OrdonnanceController::class);
    Route::resource("/soin",SoinController::class);
    Route::resource("/lab-medicament",LabController::class);
    Route::resource("/acte-medical",ActeMedicalController::class);
    Route::resource("/agenda",AgendaController::class);
    Route::resource("/demande-analyse",DemandeAnalyseController::class);
    Route::resource("/analyses",AnalyseController::class);
    Route::resource("/facture",FactureController::class);
    Route::resource("/article",ArticleController::class);
    Route::resource("/rendez-vous",RendezVous::class);
    Route::resource("/payment",PaymentController::class);
    Route::resource("/imagerie",ImagerieController::class);
    Route::resource("/charges",ChargeController::class);
    Route::resource("/mesures",MesureController::class);
    Route::resource("/tasks",TasksController::class);
    Route::resource("/tableau-perso",TableauPersonaliseController::class);
    
    Route::resource('/stock', StockController::class);
    
    Route::resource("/licence",EntityController::class);
    Route::resource("/users",UsersController::class);

    
    Route::post("/waiting-list/request",[WaitingList::class,"isWaiting"]);

    Route::post('/rendez-vous/request',[RendezVous::class,'is_today']);
    Route::get("/patient/search/{id}",[PatientController::class, "search"]);
	Route::get("/reporting/{type}",[ReportingController::class, "reports"]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
