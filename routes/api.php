<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\IncidenceController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\MetricController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Incidences (protected)
        Route::post('/incidences', [IncidenceController::class, 'store']);
        Route::put('/incidences/{incidence}', [IncidenceController::class, 'update']);
        Route::delete('/incidences/{incidence}', [IncidenceController::class, 'destroy']);
    });

    // Incidences (public)
    Route::get('/incidences', [IncidenceController::class, 'index']);
    Route::get('/incidences/{incidence}', [IncidenceController::class, 'show']);

    // Comments
    Route::get('/incidences/{incidenceId}/comments', [CommentController::class, 'index']);
    Route::post('/incidences/{incidenceId}/comments', [CommentController::class, 'store'])->middleware('auth:api');
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->middleware('auth:api');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth:api');

    // Tags
    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/tags', [TagController::class, 'store'])->middleware('auth:api');
    Route::get('/tags/{tag}', [TagController::class, 'show']);
    Route::put('/tags/{tag}', [TagController::class, 'update'])->middleware('auth:api');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->middleware('auth:api');

    // Metrics
    Route::get('/metrics', [MetricController::class, 'index'])->middleware('auth:api');

    // Users Admin
    Route::middleware(['auth:api', 'is_admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::get('/users/{id}/incidences', [UserController::class, 'incidences']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });
});
