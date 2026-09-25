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

    // Download ZIP routes
    Route::get('/timsosmed/contents/{content}/download/{collection}', [\App\Modules\TimSosmed\Http\Controllers\MediaDownloadController::class, 'downloadContentCollection'])
        ->name('timsosmed.content.download-collection');

    Route::post('/timsosmed/gallery/download-zip', [\App\Modules\TimSosmed\Http\Controllers\MediaDownloadController::class, 'downloadBatchZip'])
        ->name('timsosmed.gallery.download-zip');
});
