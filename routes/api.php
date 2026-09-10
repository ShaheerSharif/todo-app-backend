<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'auth.api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::prefix('todo')->group(function () {
        Route::get('/', [TodoController::class, 'index']);
        Route::get('/{id}', [TodoController::class, 'show']);
        Route::post('/store', [TodoController::class, 'store']);
        Route::post('/update/{id}', [TodoController::class, 'update']);
        Route::delete('/destroy/{id}', [TodoController::class, 'destroy']);
    });
});
