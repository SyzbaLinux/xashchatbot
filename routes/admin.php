<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ChatSessionController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Chatbot management
    Route::get('/transactions',    [TransactionController::class,  'index'])->name('admin.transactions');
    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('admin.payment-methods');
    Route::post('/payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggle'])->name('admin.payment-methods.toggle');
    Route::get('/chat-sessions',   [ChatSessionController::class,  'index'])->name('admin.chat-sessions');

    // User management
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
});
