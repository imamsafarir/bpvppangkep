<?php

namespace App\Modules\TimSosmed\Filament\Widgets;

use App\Modules\TimSosmed\Models\Content;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentStatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isMedsosTeam() ?? false;
    }

    protected int|array|null $columns = [
        'default' => 2, // 2 kolom di HP
        'md' => 2,
        'lg' => 4,      // 4 kolom di Desktop
    ];

    protected function getStats(): array
    {
        // Ambil semua count dalam satu query untuk efisiensi
        $counts = Content::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Hitung total untuk status 'Review' (gabungan beberapa status)
        $reviewCount = ($counts['revisi_editor'] ?? 0) +
            ($counts['revisi_planner'] ?? 0) +
            ($counts['siap_publish'] ?? 0);

        return [
            Stat::make('Butuh Aset', $counts['draft'] ?? 0)
                ->description('Draft Planner')
                ->descriptionIcon('heroicon-m-pencil')
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Contoh data dummy grafik
                ->color('gray')
                ->url(route('filament.admin.resources.contents.index', ['tableFilters[status][value]' => 'draft'])),

            Stat::make('Editing', $counts['menunggu_editor'] ?? 0)
                ->description('Proses Editor')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([15, 4, 10, 2, 12, 22, 19])
                ->color('warning')
                ->url(route('filament.admin.resources.contents.index', ['tableFilters[status][value]' => 'menunggu_editor'])),

            Stat::make('Review', $reviewCount)
                ->description('Butuh Tindakan')
                ->descriptionIcon('heroicon-m-eye')
                ->chart([2, 10, 5, 12, 8, 15, 10])
                ->color('info'),

            Stat::make('Selesai', $counts['selesai'] ?? 0)
                ->description('Sudah Live')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([5, 10, 20, 15, 25, 30, 40])
                ->color('success')
                ->url(route('filament.admin.resources.contents.index', ['tableFilters[status][value]' => 'selesai'])),
        ];
    }
}
