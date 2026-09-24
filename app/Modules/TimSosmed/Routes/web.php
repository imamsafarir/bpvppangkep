<?php

use Illuminate\Support\Facades\Route;
use App\Modules\TimSosmed\Http\Controllers\FacebookAuthController;

Route::middleware('web')->group(function () {
    // Direct access ke domain/timsosmed langsung mengarahkan untuk login admin
    Route::get('/timsosmed', function () {
        return redirect()->to('/admin/login');
    })->name('timsosmed.entry');

    Route::get('/auth/facebook', [FacebookAuthController::class, 'redirectToFacebook'])->name('facebook.login');
    Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'handleFacebookCallback'])->name('facebook.callback');

    Route::get('/privacy-policy', function () {
        return '<h1>Kebijakan Privasi</h1><p>Aplikasi ini dibuat untuk manajemen media sosial internal BPVP Pangkep. Kami tidak membagikan data Anda kepada pihak ketiga.</p>';
    });
});
