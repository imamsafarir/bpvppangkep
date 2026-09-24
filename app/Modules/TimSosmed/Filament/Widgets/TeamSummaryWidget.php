<?php

namespace App\Modules\TimSosmed\Filament\Widgets;

use App\Models\User;
use App\Modules\TimSosmed\Models\Content;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget ringkasan tim medsos — muncul sebagai header di halaman ListUsers
 * dan dapat juga digunakan di dashboard TimSosmed.
 */
class TeamSummaryWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isMedsosTeam() ?? false;
    }

    protected function getStats(): array
    {
        $plannerCount       = User::whereIn('role', ['medsos_planner', 'planner'])->count();
        $editorCount        = User::whereIn('role', ['medsos_editor', 'editor'])->count();
        $adminCount         = User::whereIn('role', ['medsos_admin_platform', 'admin_platform'])->count();
        $activeContentCount = Content::whereNotIn('status', ['selesai'])->count();

        return [
            Stat::make('Medsos Planner', $plannerCount)
                ->description('Anggota aktif')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning')
                ->icon('heroicon-o-clipboard-document-list'),

            Stat::make('Medsos Editor', $editorCount)
                ->description('Anggota aktif')
                ->descriptionIcon('heroicon-m-paint-brush')
                ->color('success')
                ->icon('heroicon-o-paint-brush'),

            Stat::make('Medsos Admin Platform', $adminCount)
                ->description('Anggota aktif')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info')
                ->icon('heroicon-o-globe-alt'),

            Stat::make('Konten Sedang Berjalan', $activeContentCount)
                ->description('Belum selesai dipublikasi')
                ->descriptionIcon('heroicon-m-fire')
                ->color($activeContentCount > 0 ? 'danger' : 'gray')
                ->icon('heroicon-o-fire'),
        ];
    }
}
