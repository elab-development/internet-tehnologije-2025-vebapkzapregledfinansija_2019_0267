<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudzetController;
use App\Http\Controllers\DokumentController;
use App\Http\Controllers\FinansijskiCiljController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\KategorijaController;
use App\Http\Controllers\PodsetnikController;
use App\Http\Controllers\TransakcijaController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('password/forgot', [ForgotPasswordController::class, 'sendResetLink']);
Route::post('password/reset', [ForgotPasswordController::class, 'resetPassword']);

Route::get('/email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/korisnik', [UserController::class, 'getUser']);
    Route::put('/korisnik', [UserController::class, 'updateProfile']);

    Route::get('/transakcije/pregled', [TransakcijaController::class, 'pregledTransakcija']);
    Route::get('/transakcije/prihodi', [TransakcijaController::class, 'mojiPrihodi']);
    Route::get('/transakcije/rashodi', [TransakcijaController::class, 'mojiRashodi']);

    Route::get('/transakcije/prihodi-paginacija', [TransakcijaController::class, 'mojiPrihodiPaginacija']);
    Route::get('/transakcije/rashodi-paginacija', [TransakcijaController::class, 'mojiRashodiPaginacija']);

    Route::get('/transakcije/prihodi-paginacija-filter', [TransakcijaController::class, 'mojiPrihodiPaginacijaFilter']);
    Route::get('/transakcije/rashodi-paginacija-filter', [TransakcijaController::class, 'mojiRashodiPaginacijaFilter']);

    Route::get('/transakcije/export', [TransakcijaController::class, 'exportCsv']);
    
});

// Budzet rute
Route::get('/budzeti', [BudzetController::class, 'index']);
Route::get('/budzeti/korisnik/{id}', [BudzetController::class, 'userBudgets']);
Route::get('/budzeti/{id}', [BudzetController::class, 'show']);
Route::post('/budzeti', [BudzetController::class, 'store']);
Route::delete('/budzeti/{id}', [BudzetController::class, 'destroy']);
Route::put('/budzeti/{id}', [BudzetController::class, 'update']);

// Kategorija rute
Route::get('/kategorije', [KategorijaController::class, 'index']);
Route::get('/kategorije/korisnik/{id}', [KategorijaController::class, 'userCategories']);
Route::get('/kategorije/{id}', [KategorijaController::class, 'show']);
Route::post('/kategorije', [KategorijaController::class, 'store']);
Route::delete('/kategorije/{id}', [KategorijaController::class, 'destroy']);
Route::put('/kategorije/{id}', [KategorijaController::class, 'update']);

// Finansijski cilj rute
Route::get('/finansijski-ciljevi', [FinansijskiCiljController::class, 'index']);
Route::get('/finansijski-ciljevi/{id}', [FinansijskiCiljController::class, 'show']);
Route::get('/finansijski-ciljevi/korisnik/{id}', [FinansijskiCiljController::class, 'userFinancialGoals']);
Route::post('/finansijski-ciljevi', [FinansijskiCiljController::class, 'store']);
Route::delete('/finansijski-ciljevi/{id}', [FinansijskiCiljController::class, 'destroy']);
Route::put('/finansijski-ciljevi/{id}', [FinansijskiCiljController::class, 'update']);

// Dokument rute
Route::resource('/dokumenti', DokumentController::class);
/* Route::get('/dokumenti', [DokumentController::class, 'index']);
Route::get('/dokumenti/{id}', [DokumentController::class, 'show']);
Route::post('/dokumenti', [DokumentController::class, 'store']);
Route::delete('/dokumenti/{id}', [DokumentController::class, 'destroy']);
Route::put('/dokumenti/{id}', [DokumentController::class, 'update']); */

// Transakcija rute
Route::get('/transakcije', [TransakcijaController::class, 'index']);
Route::get('/transakcije/{id}', [TransakcijaController::class, 'show']);
Route::post('/transakcije', [TransakcijaController::class, 'store']);
Route::delete('/transakcije/{id}', [TransakcijaController::class, 'destroy']);
Route::put('/transakcije/{id}', [TransakcijaController::class, 'update']);
Route::get('/transakcije/korisnik/{id}', [TransakcijaController::class, 'userTransactions']);
Route::get('/transakcije/korisnik/{idKorisnik}/kategorija/{idKategorija}', [TransakcijaController::class, 'userCategoryTransactions']);
Route::put('/transakcije/{id}/valuta', [TransakcijaController::class, 'updateValuta']);

// Podsetnik rute
Route::get('/podsetnici', [PodsetnikController::class, 'index']);
Route::get('/podsetnici/{id}', [PodsetnikController::class, 'show']);
Route::get('/podsetnici/korisnik/{id}', [PodsetnikController::class, 'userReminders']);
Route::post('/podsetnici', [PodsetnikController::class, 'store']);
Route::delete('/podsetnici/{id}', [PodsetnikController::class, 'destroy']);
Route::put('/podsetnici/{id}', [PodsetnikController::class, 'update']);
