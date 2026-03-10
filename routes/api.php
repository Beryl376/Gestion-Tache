<?php 
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\TacheController;

use Illuminate\Support\Facades\Route;



    Route::get('/taches', [TacheController::class, 'index']);
    Route::post('/taches', [TacheController::class, 'store']);
    Route::get('/taches/{Tache}', [TacheController::class, 'show']);   
    Route::put('/taches/{Tache}', [TacheController::class, 'update']);
    Route::delete('/taches/{Tache}', [TacheController::class, 'delete']);

