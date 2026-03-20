<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\IncidenceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::post('/incidences', [IncidenceController::class, 'store']);
        Route::put('/incidences/{id}', [IncidenceController::class, 'update']);
        Route::delete('/incidences/{id}', [IncidenceController::class, 'destroy']);
    });

    // Public endpoints
    Route::get('/incidences', [IncidenceController::class, 'index']);
    Route::get('/incidences/{id}', [IncidenceController::class, 'show']);

});
