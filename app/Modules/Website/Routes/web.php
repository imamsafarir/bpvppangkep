<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Website\Http\Controllers\HomeController;
use App\Modules\Website\Http\Controllers\ProfilController;
use App\Modules\Website\Models\InformasiPublik;
use App\Modules\Website\Models\Jdih;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Website Module - Public Routes
|--------------------------------------------------------------------------
*/

Route::middleware([\App\Http\Middleware\BrowserCacheMiddleware::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::prefix('profil')->name('profil.')->group(function () {
        Route::get('/sambutan-kepala', [ProfilController::class, 'sambutan'])->name('sambutan');
        Route::get('/tentang-kami', [ProfilController::class, 'tentangKami'])->name('tentang-kami');
        Route::get('/ppid-pelayanan', [ProfilController::class, 'ppid'])->name('ppid');
        Route::get('/visi-misi', [ProfilController::class, 'visiMisi'])->name('visi-misi');
        Route::get('/tugas-fungsi', [ProfilController::class, 'tugasFungsi'])->name('tugas-fungsi');
        Route::get('/struktur-organisasi', [ProfilController::class, 'strukturOrganisasi'])->name('struktur');
        Route::get('/pejabat-struktural', [ProfilController::class, 'pejabat'])->name('pejabat');
    });

    Route::prefix('informasi')->name('informasi.')->group(function () {
        Route::get('/kejuruan', [ProfilController::class, 'kejuruan'])->name('kejuruan');
        Route::get('/gedung-fasilitas', [ProfilController::class, 'fasilitas'])->name('fasilitas');
        Route::get('/ruang-kelas-workshop', [ProfilController::class, 'workshop'])->name('workshop');
        Route::get('/alumni', [ProfilController::class, 'alumni'])->name('alumni');
        Route::get('/testimoni', [ProfilController::class, 'testimoni'])->name('testimoni');
    });

    Route::prefix('informasi-publik')->name('publik.')->group(function () {
        Route::get('/berkala', [ProfilController::class, 'berkala'])->name('berkala');
        Route::get('/serta-merta', [ProfilController::class, 'sertaMerta'])->name('serta-merta');
        Route::get('/setiap-saat', [ProfilController::class, 'setiapSaat'])->name('setiap-saat');
    });

    Route::prefix('pelayanan-publik')->name('pelayanan.')->group(function () {
        Route::get('/maklumat', [ProfilController::class, 'maklumat'])->name('maklumat');
        Route::get('/standar-pelayanan', [ProfilController::class, 'standar'])->name('standar');
        Route::get('/alur-pelayanan', [ProfilController::class, 'alur'])->name('alur');
        Route::get('/survey-kepuasan', [ProfilController::class, 'surveyKepuasan'])->name('survey-kepuasan');
        Route::get('/survey-kebutuhan', [ProfilController::class, 'surveyKebutuhan'])->name('survey-kebutuhan');
        Route::get('/survey-kebekerjaan', [ProfilController::class, 'surveyKebekerjaan'])->name('survey-kebekerjaan');
        Route::get('/indeks-kepuasan', [ProfilController::class, 'indeksKepuasan'])->name('indeks-kepuasan');
    });

    Route::prefix('berita-informasi')->name('berita.')->group(function () {
        Route::get('/daftar-berita', [ProfilController::class, 'berita'])->name('index');
        Route::get('/berita/{id}', [ProfilController::class, 'detailBerita'])->name('show');
        Route::get('/galeri-kegiatan', [ProfilController::class, 'galeri'])->name('galeri');
    });

    Route::get('/jdih', [ProfilController::class, 'jdih'])->name('jdih.index');
});

Route::get('/download/informasi/{id}', function ($id) {
    $dokumen = InformasiPublik::findOrFail($id);
    $dokumen->increment('jumlah_diunduh');
    return response()->download(Storage::disk('public')->path($dokumen->file_path));
})->name('download.informasi');

Route::get('/download/jdih/{id}', function ($id) {
    $dokumen = Jdih::findOrFail($id);
    $dokumen->increment('jumlah_diunduh');
    return response()->download(Storage::disk('public')->path($dokumen->file_dokumen));
})->name('download.jdih');

Route::get('/galeri/{id}/download', [ProfilController::class, 'download'])->name('galeri.download');
