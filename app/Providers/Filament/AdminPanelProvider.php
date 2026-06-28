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
use App\Filament\Widgets\WebsiteStatsOverview;
use App\Filament\Widgets\BeritaDanGaleriChart;
use App\Filament\Widgets\InformasiPublikChart;
use App\Filament\Widgets\JdihChart;
use App\Filament\Widgets\AnalitikUnduhanChart;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\WebsiteSetting;
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

            ->brandName('Admin Website BPVP Pangkep')
            ->favicon($faviconUrl)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
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
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
