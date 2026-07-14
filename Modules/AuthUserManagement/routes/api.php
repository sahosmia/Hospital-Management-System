<?php

use Illuminate\Support\Facades\Route;
use Modules\AuthUserManagement\Http\Controllers\AuthController;

// --- PUBLIC AUTH ROUTES ---
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('otp/request', [AuthController::class, 'otpRequest']);
    Route::post('otp/verify', [AuthController::class, 'otpVerify']);
    Route::get('google', [AuthController::class, 'googleLogin']);
    Route::get('google/callback', [AuthController::class, 'googleCallback']);
});

// --- PROTECTED AUTH ROUTES ---
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
});
