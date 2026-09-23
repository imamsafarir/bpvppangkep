<?php

use App\Modules\Shortlink\Http\Controllers\ShortlinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Shortlink Module - Web Routes (Session, CSRF, & Errors enabled)
|--------------------------------------------------------------------------
*/

Route::middleware('web')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Shortlink Module - Public Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/s/{code}', [ShortlinkController::class, 'handle'])->name('shortlink.handle');
    Route::post('/s/{code}/submit', [ShortlinkController::class, 'submit'])->name('shortlink.submit');
    Route::get('/s/{code}/qr', [ShortlinkController::class, 'downloadQr'])->name('shortlink.qr');

    /*
    |--------------------------------------------------------------------------
    | Shortlink Module - Admin Routes (auth required)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {
        Route::get('/admin/shortlink/template', [ShortlinkController::class, 'downloadTemplate'])->name('admin.shortlink.template');
        Route::get('/admin/shortlink/export', [ShortlinkController::class, 'exportShortlinksCsv'])->name('admin.shortlink.export');
        Route::post('/admin/shortlink/import', [ShortlinkController::class, 'importShortlinks'])->name('admin.shortlink.import');
        Route::get('/admin/shortlink/leads/export', [ShortlinkController::class, 'exportLeadsCsv'])->name('admin.shortlink.leads.export');
    });
});
