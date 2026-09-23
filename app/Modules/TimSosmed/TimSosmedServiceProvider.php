<?php

namespace App\Modules\TimSosmed;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Modules\TimSosmed\Livewire\FloatingChat;

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

        // Daftarkan komponen Livewire FloatingChat
        Livewire::component('floating-chat', FloatingChat::class);
    }
}
