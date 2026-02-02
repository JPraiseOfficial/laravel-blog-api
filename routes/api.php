<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// AUTH ROUTES
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

// USER ROUTES
Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'registerUser');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', 'profile');
        Route::get('/user/{id}', 'profile');
        Route::patch('/user/update', 'update');
        Route::delete('/user/delete', 'destroy');
    });
});
