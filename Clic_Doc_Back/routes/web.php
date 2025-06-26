<?php

use App\Http\Controllers\AnalyseController;
use App\Http\Controllers\OrdonnanceController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\EXLSExportController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('upload/show/{fileName}', [UploadController::class, 'show']);

Route::get("/ordonnance/{id}",[OrdonnanceController::class,"imprimer"]);
Route::get("/analyse/{id}",[AnalyseController::class,"imprimer"]);
Route::get("/cnss/{id}",[ConsultationController::class,"cnss"]);
Route::get("/certificat/{type}/{id}/{docteur}",[PrintController::class,"certificat"]);

Route::get('/facturation/print/{factureId}', [PrintController::class, 'printFacture'])->name('facturation.print');
Route::get('/facturation/{id}/{docteur}', [PrintController::class, 'facturation'])->name('facturation');

Route::get('/DataTableCaisse/{doctor_id}', [FactureController::class, 'displayPayments'])->name('DataTableFactures.display');

Route::get('/EXLS/export/{doctor_id}', [EXLSExportController::class, 'exportTransactions'])->name('transactions.export');
Route::get('/export-patients', [EXLSExportController::class, 'exportPatients'])->name('patients.export');


Route::get('/recu/{payment_value}/{doctor_id}', [PrintController::class, 'generateRecu'])->name('recu.generate');



Route::get('/patients/{doctor_id?}', [PatientController::class, 'displayPatients'])->name('patients_list.display');



Route::prefix('documents')->group(function () {
    Route::post('/generate', [DocumentController::class, 'generate']);
    Route::get('/{documentType}', [DocumentController::class, 'view']);
});

// Keep existing certificate routes for backward compatibility
Route::get('/certificat/aptitude/{patient_uid}/{doctor_id}', [DocumentController::class, 'certificatAptitude']);
Route::get('/certificat/repos/{patient_uid}/{doctor_id}', [DocumentController::class, 'certificatRepos']);
Route::get('/facturation/{patient_uid}/{doctor_id}', [DocumentController::class, 'facturation']);
