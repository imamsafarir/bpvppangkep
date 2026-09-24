<?php

namespace App\Modules\TimSosmed\Filament\Widgets;

use App\Modules\TimSosmed\Filament\Pages\StatistikTim;
use App\Modules\TimSosmed\Models\Content;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class TimSosmedStatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    public static function canView(): bool
    {
        return Auth::user()?->isMedsosTeam() ?? false;
    }

    protected int|array|null $columns = [
        'default' => 2,
        'md' => 2,
        'lg' => 4,
    ];

    protected function getStats(): array
    {
        $summary = StatistikTim::getTeamSummary();

        $formatNumber = fn($value) => number_format((int) ($value ?? 0), 0, ',', '.');

        $totalMembers = $summary['total_members'] ?? 0;
        $totalContribution = $summary['total_contribution_all'] ?? 0;
        $weekContribution = $summary['total_contribution_week'] ?? 0;
        $activeContents = $summary['total_active_contents'] ?? 0;
        $weekLabel = $summary['week_label'] ?? 'Minggu Ini';

        // Hitung tren aktivitas 7 hari terakhir secara riil
        $dailyTrends = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->toDateString();
            return Content::query()->whereDate('updated_at', $date)->count();
        })->toArray();

        // Fallback kurva jika data testing masih kosong
        if (array_sum($dailyTrends) === 0) {
            $dailyTrends = [0, 1, 1, 2, 2, 3, max(1, $weekContribution)];
        }

        $cssInject = "
            <style>
                .st-stat-card {
                    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                    border-radius: 16px !important;
                    position: relative !important;
                    overflow: hidden !important;
                }
                .st-stat-card:hover {
                    transform: translateY(-3px) !important;
                    box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08) !important;
                }
                .st-card-blue {
                    background: #f0f7ff !important;
                    border: 1px solid #bfdbfe !important;
                    border-left: 5px solid #3b82f6 !important;
                }
                .st-card-green {
                    background: #f0fdf4 !important;
                    border: 1px solid #bbf7d0 !important;
                    border-left: 5px solid #10b981 !important;
                }
                .st-card-amber {
                    background: #fffbeb !important;
                    border: 1px solid #fde68a !important;
                    border-left: 5px solid #f59e0b !important;
                }
                .st-card-purple {
                    background: #faf5ff !important;
                    border: 1px solid #e9d5ff !important;
                    border-left: 5px solid #8b5cf6 !important;
                }

                .dark .st-card-blue {
                    background: rgba(59, 130, 246, 0.08) !important;
                    border-color: rgba(59, 130, 246, 0.25) !important;
                    border-left-color: #3b82f6 !important;
                }
                .dark .st-card-green {
                    background: rgba(16, 185, 129, 0.08) !important;
                    border-color: rgba(16, 185, 129, 0.25) !important;
                    border-left-color: #10b981 !important;
                }
                .dark .st-card-amber {
                    background: rgba(245, 158, 11, 0.08) !important;
                    border-color: rgba(245, 158, 11, 0.25) !important;
                    border-left-color: #f59e0b !important;
                }
                .dark .st-card-purple {
                    background: rgba(139, 92, 246, 0.08) !important;
                    border-color: rgba(139, 92, 246, 0.25) !important;
                    border-left-color: #8b5cf6 !important;
                }
            </style>
        ";

        return [
            Stat::make('Total Anggota Medsos', $formatNumber($totalMembers))
                ->description(new HtmlString($cssInject . 'Pengguna aktif peran medsos'))
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([3, 4, 5, 5, 6, 6, max(1, $totalMembers)])
                ->color('info')
                ->extraAttributes([
                    'class' => 'st-stat-card st-card-blue',
                ]),

            Stat::make('Total Kontribusi', $formatNumber($totalContribution))
                ->description('Bahan, edit & publikasi')
                ->descriptionIcon('heroicon-m-trophy')
                ->chart($dailyTrends)
                ->color('success')
                ->extraAttributes([
                    'class' => 'st-stat-card st-card-green',
                ]),

            Stat::make('Kontribusi Minggu Ini', $formatNumber($weekContribution))
                ->description($weekLabel)
                ->descriptionIcon('heroicon-m-fire')
                ->chart($dailyTrends)
                ->color('warning')
                ->extraAttributes([
                    'class' => 'st-stat-card st-card-amber',
                ]),

            Stat::make('Konten Berjalan', $formatNumber($activeContents))
                ->description('Draft, editing & siap tayang')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([2, 3, 2, 4, 3, 4, max(1, $activeContents)])
                ->color($activeContents > 0 ? 'danger' : 'gray')
                ->extraAttributes([
                    'class' => 'st-stat-card st-card-purple',
                ]),
        ];
    }
}
