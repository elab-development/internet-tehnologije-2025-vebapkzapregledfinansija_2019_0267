<?php

use App\Http\Controllers\BudzetController;
use App\Http\Controllers\KategorijaController;
use App\Http\Controllers\FinansijskiCiljController;
use App\Http\Controllers\DokumentController;
use App\Http\Controllers\TransakcijaController;
use App\Http\Controllers\PodsetnikController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

//Budzet rute
Route::get('/budzeti', [BudzetController::class, 'index']);
Route::get('/budzeti/{id}', [BudzetController::class, 'show']);
Route::post('/budzeti', [BudzetController::class, 'store']);
Route::delete('/budzeti/{id}', [BudzetController::class, 'destroy']);
Route::put('/budzeti/{id}', [BudzetController::class, 'update']);

//Kategorija rute
Route::get('/kategorije', [KategorijaController::class, 'index']);
Route::get('/kategorije/{id}', [KategorijaController::class, 'show']);
Route::post('/kategorije', [KategorijaController::class, 'store']);
Route::delete('/kategorije/{id}', [KategorijaController::class, 'destroy']);
Route::put('/kategorije/{id}', [KategorijaController::class, 'update']);

//Finansijski cilj rute
Route::get('/finansijski_ciljevi', [FinansijskiCiljController::class, 'index']);
Route::get('/finansijski_ciljevi/{id}', [FinansijskiCiljController::class, 'show']);
Route::post('/finansijski_ciljevi', [FinansijskiCiljController::class, 'store']);
Route::delete('/finansijski_ciljevi/{id}', [FinansijskiCiljController::class, 'destroy']);
Route::put('/finansijski_ciljevi/{id}', [FinansijskiCiljController::class, 'update']);

//Dokument rute
Route::resource('/dokumenti', DokumentController::class);
 /* Route::get('/dokumenti', [DokumentController::class, 'index']);
 Route::get('/dokumenti/{id}', [DokumentController::class, 'show']);
 Route::post('/dokumenti', [DokumentController::class, 'store']);
 Route::delete('/dokumenti/{id}', [DokumentController::class, 'destroy']);
 Route::put('/dokumenti/{id}', [DokumentController::class, 'update']); */

 //Transakcija rute
Route::get('/transakcije', [TransakcijaController::class, 'index']);
Route::get('/transakcije/{id}', [TransakcijaController::class, 'show']);
Route::post('/transakcije', [TransakcijaController::class, 'store']);   
Route::delete('/transakcije/{id}', [TransakcijaController::class, 'destroy']);
Route::put('/transakcije/{id}', [TransakcijaController::class, 'update']);

//Podsetnik rute
Route::get('/podsetnici', [PodsetnikController::class, 'index']);
Route::get('/podsetnici/{id}', [PodsetnikController::class, 'show']);
Route::post('/podsetnici', [PodsetnikController::class, 'store']);
Route::delete('/podsetnici/{id}', [PodsetnikController::class, 'destroy']);
Route::put('/podsetnici/{id}', [PodsetnikController::class, 'update']); 
