<?php

use App\Http\Controllers\Auth\OTPVerificationController;
use App\Http\Controllers\Webhooks\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [\App\Http\Controllers\WebsiteController::class,'welcome'])->name('welcome');

// Generic Dashboard Route - Redirects based on user role
Route::get('/dashboard', function () {
    // This route is caught by RedirectDashboardByRole middleware
    // and redirected to /user/dashboard or /admin/dashboard
    abort(404);
})->middleware(['auth', 'verified', 'redirect.dashboard.by.role'])->name('dashboard');

// OTP Verification Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/otp/verify', [OTPVerificationController::class, 'show'])->name('otp.show');
    Route::post('/otp/send', [OTPVerificationController::class, 'send'])->name('otp.send');
    Route::post('/otp/verify', [OTPVerificationController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/resend', [OTPVerificationController::class, 'resend'])->name('otp.resend');
});

// WhatsApp Webhook (No middleware - public)
Route::withoutMiddleware('web')->group(function () {
    Route::get('/webhook', [WhatsAppWebhookController::class, 'verify']);
    Route::post('/webhook', [WhatsAppWebhookController::class, 'handle']);
});

require __DIR__.'/admin.php';
require __DIR__.'/user.php';
