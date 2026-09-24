<?php

namespace App\Modules\Website;

use Illuminate\Support\ServiceProvider;

class WebsiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Daftarkan route milik modul Website
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        // Daftarkan view dengan namespace "website::"
        // Penggunaan: view('website::home')
        // Penggunaan: view('website::profil.sambutan')
        // Penggunaan: view('website::filament.manage-profil')
        $this->loadViewsFrom(__DIR__ . '/Views', 'website');

        // Daftarkan komponen Blade agar tag <x-home.hero /> dapat ditemukan dari modul
        \Illuminate\Support\Facades\Blade::anonymousComponentPath(__DIR__ . '/Views/components', '');

        // Daftarkan migration milik modul ini
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Bersihkan cache secara otomatis saat admin memperbarui konten di Filament
        $clearCache = function () {
            \Illuminate\Support\Facades\Cache::forget('website_home_data');
            \Illuminate\Support\Facades\Cache::forget('website_common_data');
        };

        \App\Modules\Website\Models\WebsiteSetting::saved($clearCache);
        \App\Modules\Website\Models\WebsiteSetting::deleted($clearCache);
        \App\Modules\Website\Models\BeritaDanGaleri::saved($clearCache);
        \App\Modules\Website\Models\BeritaDanGaleri::deleted($clearCache);
        \App\Modules\Website\Models\Informasi::saved($clearCache);
        \App\Modules\Website\Models\Informasi::deleted($clearCache);
        \App\Modules\Website\Models\InformasiPublik::saved($clearCache);
        \App\Modules\Website\Models\InformasiPublik::deleted($clearCache);
        \App\Modules\Website\Models\PelayananPublik::saved($clearCache);
        \App\Modules\Website\Models\PelayananPublik::deleted($clearCache);
        \App\Modules\Website\Models\Jdih::saved($clearCache);
        \App\Modules\Website\Models\Jdih::deleted($clearCache);
    }
}
