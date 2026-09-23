<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use App\Modules\Website\Filament\Widgets\WebsiteStatsOverview;
use App\Modules\Website\Filament\Widgets\BeritaDanGaleriChart;
use App\Modules\Website\Filament\Widgets\InformasiPublikChart;
use App\Modules\Website\Filament\Widgets\JdihChart;
use App\Modules\Website\Filament\Widgets\AnalitikUnduhanChart;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
// use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modules\Website\Models\WebsiteSetting;
use Filament\Pages\Auth\Login;
use Filament\Forms\Components\TextInput;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $faviconUrl = null;

        try {
            if (config('database.default') && Schema::hasTable('website_settings')) {
                $settings = WebsiteSetting::query()->first();

                if ($settings?->favicon_path) {
                    $faviconUrl = asset('storage/' . $settings->favicon_path);
                }
            }
        } catch (\Throwable $e) {
            // Safe fallback if connection fails during boot
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')

            // 🟢 MODIFIKASI FORM LOGIN (HANYA USERNAME)
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)

            ->homeUrl(fn(): string => auth()->user()?->role === 'shortlink' ? url('/admin/manage-shortlink') : url('/admin'))

            ->brandName('Admin Website BPVP Pangkep')
            ->favicon($faviconUrl)
            ->colors([
                'primary' => Color::Indigo,
            ])
            // 🟢 TAMBAHKAN BARIS INI UNTUK MEMAKSA LIGHT MODE SAJA:
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverPages(in: app_path('Modules/Shortlink/Filament/Pages'), for: 'App\Modules\Shortlink\Filament\Pages')
            ->discoverPages(in: app_path('Modules/Website/Filament/Pages'), for: 'App\Modules\Website\Filament\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->widgets([
                WebsiteStatsOverview::class,
                BeritaDanGaleriChart::class,
                InformasiPublikChart::class,
                JdihChart::class,
                AnalitikUnduhanChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                // PreventRequestForgery::class,
                ValidateCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
