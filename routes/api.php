<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    // Public
    Route::post('/register', 'register');
    Route::post('/login', 'login');

    // Password Reset
    Route::middleware('guest')->group(function () {
        Route::post('/forgot-password', 'forgotPassword');
        Route::get('/reset-password/{token}', 'frontendResetPasswordRedirect')->name('password.reset');
        Route::post('/reset-password', 'resetPassword')->name('password.update');
    });

    // Email Verification
    Route::get('/email/verify/{id}/{hash}', 'verifyEmail')
        ->middleware(['auth:sanctum', 'signed'])
        ->name('verification.verify');

    Route::post('/email/sendVerificationLink', 'sendEmailVerification')
        ->middleware(['auth:sanctum', 'throttle:6,1'])
        ->name('verification.send');

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
        Route::post('/change-password', 'changePassword');
    });
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::controller(UserController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/user', 'profile');
    Route::get('/user/{id}', 'profile');
    Route::patch('/user', 'update');
    Route::delete('/user', 'destroy');
});

/*
|--------------------------------------------------------------------------
| Post Routes
|--------------------------------------------------------------------------
*/
Route::controller(PostController::class)->middleware('auth:sanctum')->group(function () {
    // Resource Routes
    Route::get('/posts', 'index');
    Route::post('/posts', 'store');
    Route::get('/posts/{post}', 'show');
    Route::match(['put', 'patch'], '/posts/{post}', 'update');
    Route::delete('/posts/{post}', 'destroy');

    // Custom Post Routes
    Route::get('/feed', 'getLatestPosts');
    Route::get('/{user}/posts', 'getOtherUsersPosts');
});

/*
|--------------------------------------------------------------------------
| Comment Routes
|--------------------------------------------------------------------------
*/
Route::controller(CommentController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/comments', 'index');
    Route::post('/comments', 'store');
    Route::get('/comments/{comment}', 'show');
    Route::match(['put', 'patch'], '/comments/{comment}', 'update');
    Route::delete('/comments/{comment}', 'destroy');
});

/*
|--------------------------------------------------------------------------
| Like Routes
|--------------------------------------------------------------------------
*/
Route::controller(LikeController::class)->middleware('auth:sanctum')->group(function () {
    Route::post('/like-toggle', 'toggle');
});
