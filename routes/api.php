<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ====== AUTH ROUTES =========
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
        Route::post('/change-password', 'changePassword');
    });
});
// Verify email handler
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Send Email Verification Link
Route::post('/email/sendVerificationLink', [AuthController::class, 'sendEmailVerification'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

Route::middleware('guest')->controller(AuthController::class)->group(function () {
    // Forgot Password
    Route::post('/forgot-password', 'forgotPassword');
    // Reset Password Frontend
    Route::get('/reset-password/{token}', 'frontendResetPasswordRedirect')->name('password.reset');
    // Reset Password Backend
    Route::post('/reset-password', 'resetPassword')->name('password.update');
});

/*
========================================
*/
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

// LIKE ROUTE
Route::middleware('auth:sanctum')->controller(LikeController::class)->group(function () {
    Route::post('/like-toggle', 'toggle');
    // Route::get('/posts/create', 'create');
    // Route::post('/posts', 'store');
});
