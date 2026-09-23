<?php

namespace App\Modules\Website\Filament\Widgets;

use App\Modules\Website\Models\Jdih;
use Filament\Widgets\ChartWidget;

class JdihChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->role !== 'shortlink';
    }

    // ✅ Aman dari Fatal Error: Properti non-static sesuai dokumentasi resmi
    protected ?string $heading = '⚖️ Status Produk Hukum JDIH';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $berlaku = Jdih::query()->where('status_peraturan', 'berlaku')->count('*');
        $tidakBerlaku = Jdih::query()->where('status_peraturan', 'tidak_berlaku')->count('*');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peraturan',
                    'data' => [$berlaku, $tidakBerlaku],

                    // 🎨 WARNA WARNI: Hijau untuk status aktif, Merah untuk status non-aktif
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.85)',   // Emerald Green (Berlaku)
                        'rgba(239, 68, 68, 0.85)',   // Rose Red (Tidak Berlaku)
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 1.5,
                ],
            ],
            'labels' => [
                '✅ Peraturan Masih Berlaku',
                '❌ Peraturan Sudah Dicabut',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
