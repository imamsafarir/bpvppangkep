<?php

namespace App\Modules\TimSosmed;

use App\Modules\TimSosmed\Observers\ContentMediaObserver;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TimSosmedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Daftarkan route modul Tim Sosmed
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        // Daftarkan view dengan namespace "timsosmed::"
        $this->loadViewsFrom(__DIR__ . '/Views', 'timsosmed');

        // Daftarkan migrasi internal modul
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Daftarkan komponen Livewire Diskusi Konten
        Livewire::component('content-comments', \App\Modules\TimSosmed\Livewire\ContentComments::class);

        // Observer: dispatch job kompresi AV1 saat video baru diupload ke koleksi TimSosmed
        Media::observe(ContentMediaObserver::class);
    }
}
