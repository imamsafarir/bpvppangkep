<?php

namespace App\Modules\Website\Filament\Widgets;

use App\Modules\Website\Models\BeritaDanGaleri;
use Filament\Widgets\ChartWidget;

class BeritaDanGaleriChart extends ChartWidget
{
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user ? ($user->isAdmin() || $user->isWebsite()) : false;
    }

    // ✅ SUDAH DIPERBAIKI: Menghapus keyword 'static' agar sesuai dengan dokumentasi resmi Filament
    protected ?string $heading = '📊 Proporsi Konten: Berita & Galeri';

    // Variabel sort tetap menggunakan static karena merupakan konfigurasi bawaan Base Widget
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $jumlahBerita = BeritaDanGaleri::query()->where('jenis', 'berita')->count('*');
        $jumlahGaleri = BeritaDanGaleri::query()->where('jenis', 'galeri')->count("*");

        return [
            'datasets' => [
                [
                    'label' => 'Total Diterbitkan',
                    'data' => [$jumlahBerita, $jumlahGaleri],
                    'backgroundColor' => [
                        'rgba(99, 102, 241, 0.85)', // Indigo
                        'rgba(34, 197, 94, 0.85)',  // Green
                    ],
                    'borderColor' => [
                        'rgb(99, 102, 241)',
                        'rgb(34, 197, 94)',
                    ],
                    'borderWidth' => 1.5,
                ],
            ],
            'labels' => [
                '📰 Artikel Berita',
                '📸 Galeri Kegiatan',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
