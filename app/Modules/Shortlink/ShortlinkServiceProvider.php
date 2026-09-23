<?php

namespace App\Modules\Shortlink;

use Illuminate\Support\ServiceProvider;

class ShortlinkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Daftarkan route milik modul Shortlink
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        // Daftarkan view dengan namespace "shortlink::"
        // Penggunaan: view('shortlink::capture'), view('shortlink::thankyou')
        // Penggunaan filament: view('shortlink::filament.manage-shortlink')
        $this->loadViewsFrom(__DIR__ . '/Views', 'shortlink');

        // Daftarkan migration milik modul ini
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}
