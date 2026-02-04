<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// AUTH ROUTES
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
        Route::post('/change-password', 'changePassword');
    });
});

// USER ROUTES
Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'registerUser');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', 'profile');
        Route::get('/user/{id}', 'profile');
        Route::patch('/user', 'update');
        Route::delete('/user', 'destroy');
    });
});

// POST ROUTES
Route::middleware('auth:sanctum')->apiResource('posts', PostController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/{user}/posts', [PostController::class, 'getOtherUsersPosts']);
    Route::get('/feed', [PostController::class, 'getLatestPosts']);
});

// COMMENT ROUTES
Route::middleware('auth:sanctum')->apiResource('comments', CommentController::class);
