<?php

 
use App\Http\Controllers\Webhooks\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [\App\Http\Controllers\WebsiteController::class,'welcome'])->name('welcome');

  
// Redirect /dashboard to the role-appropriate dashboard
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $role = \App\Enums\UserRole::tryFrom(auth()->user()->role);
    return redirect($role?->redirectPath() ?? '/');
})->middleware('auth')->name('dashboard');

Route::get('/webhook', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
Route::post('/webhook', [WhatsAppWebhookController::class, 'handle'])->name('whatsapp.webhook.handle');

require __DIR__.'/admin.php';
require __DIR__.'/user.php';
