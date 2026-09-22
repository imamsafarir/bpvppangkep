<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \Filament\Auth\Http\Responses\Contracts\LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // 🟢 POTONG PANJANG STRING DEFAULT AGAR MUAT DI CPANEL
        Schema::defaultStringLength(191);

        // 🟢 LOG AKTIVITAS LOGIN & LOGOUT USER
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            try {
                \App\Models\AuthenticationLog::create([
                    'user_id'    => $event->user?->id,
                    'event_type' => 'login',
                    'username'   => $event->user?->username ?? $event->user?->name,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Silently fail agar tidak menghalangi proses login jika terjadi error logging
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            try {
                \App\Models\AuthenticationLog::create([
                    'user_id'    => $event->user?->id,
                    'event_type' => 'logout',
                    'username'   => $event->user?->username ?? $event->user?->name,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Silently fail agar tidak menghalangi proses logout
            }
        });
    }
}
