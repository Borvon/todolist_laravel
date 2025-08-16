<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\TaskController;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function()
    {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::get('/tasks/{id}', [TaskController::class, 'show']);
    });