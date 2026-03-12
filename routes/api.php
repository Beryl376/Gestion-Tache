<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TacheController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Routes publiques (non authentifiées)
*/
Route::post('/register', [UserController::class, 'register']);
Route::post('/login',    [UserController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Routes protégées par Sanctum
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Authentification
    Route::post('/logout',  [UserController::class, 'logout']);
    Route::get('/profile',  [UserController::class, 'profile']);
    Route::post('/report/generate', [\App\Http\Controllers\Api\ReportController::class, 'generate']);

    // CRUD Tâches (RESTful)
    Route::get('/taches',           [TacheController::class, 'index']);
    Route::post('/taches',          [TacheController::class, 'store']);
    Route::get('/taches/{tache}',   [TacheController::class, 'show']);
    Route::put('/taches/{tache}',   [TacheController::class, 'update']);
    Route::delete('/taches/{tache}',[TacheController::class, 'destroy']);
});
