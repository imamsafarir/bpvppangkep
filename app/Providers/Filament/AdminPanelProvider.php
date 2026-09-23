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
use App\Modules\TimSosmed\Filament\Widgets\BebanKerjaOverview;
use App\Modules\TimSosmed\Filament\Widgets\MyTasksTable;
use App\Modules\TimSosmed\Filament\Widgets\ContentChart;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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

            ->brandName('Portal BPVP Pangkep')
            ->favicon($faviconUrl)
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
            ])
            // 🟢 FORCE LIGHT MODE & COLLAPSIBLE SIDEBAR:
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('15s')

            // Auto-discover Resources
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverResources(in: app_path('Modules/TimSosmed/Filament/Resources'), for: 'App\Modules\TimSosmed\Filament\Resources')

            // Auto-discover Pages
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverPages(in: app_path('Modules/Shortlink/Filament/Pages'), for: 'App\Modules\Shortlink\Filament\Pages')
            ->discoverPages(in: app_path('Modules/Website/Filament/Pages'), for: 'App\Modules\Website\Filament\Pages')
            ->discoverPages(in: app_path('Modules/TimSosmed/Filament/Pages'), for: 'App\Modules\TimSosmed\Filament\Pages')

            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->widgets([
                WebsiteStatsOverview::class,
                BeritaDanGaleriChart::class,
                InformasiPublikChart::class,
                JdihChart::class,
                AnalitikUnduhanChart::class,
                BebanKerjaOverview::class,
                MyTasksTable::class,
                ContentChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                ValidateCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // HOOK: Komponen Chat di Akhir Body
            ->renderHook(
                'panels::body.end',
                fn(): string => Auth::check()
                    ? Blade::render("@livewire('floating-chat')")
                    : ''
            )
            // HOOK: PWA Manifest & Service Worker
            ->renderHook(
                'panels::head.done',
                fn(): string => '
                <link rel="manifest" href="/manifest.json">
                <meta name="theme-color" content="#4f46e5">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <script>
                    if ("serviceWorker" in navigator) {
                        navigator.serviceWorker.register("/sw.js");
                    }
                </script>
                '
            );
    }
}
