<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;

Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('user.profile.password');

    // Two-Factor Authentication
    Route::post('/two-factor-authentication', [ProfileController::class, 'enableTwoFactor'])->name('user.two-factor.enable');
    Route::delete('/two-factor-authentication', [ProfileController::class, 'disableTwoFactor'])->name('user.two-factor.disable');
    Route::post('/recovery-codes', [ProfileController::class, 'getRecoveryCodes'])->name('user.recovery-codes');
});
