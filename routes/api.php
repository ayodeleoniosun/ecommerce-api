<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('token/verify', [AuthController::class, 'verifyToken']);
    Route::post('token/resend', [AuthController::class, 'resendToken']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
