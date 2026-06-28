<?php

namespace App\Filament\Widgets;

use App\Models\InformasiPublik;
use Filament\Widgets\ChartWidget;

class InformasiPublikChart extends ChartWidget
{
    // ✅ Aman dari Fatal Error: Properti non-static sesuai dokumentasi resmi
    protected ?string $heading = '📊 Distribusi Dokumen Informasi Publik (PPID)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // 💡 Menggunakan query() agar VS Code Intelephense tidak memunculkan garis merah
        $berkala = InformasiPublik::query()->where('kategori', 'berkala')->count('*');
        $setiapSaat = InformasiPublik::query()->where('kategori', 'setiap_saat')->count('*');
        $sertaMerta = InformasiPublik::query()->where('kategori', 'serta_merta')->count('*');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Dokumen',
                    'data' => [$berkala, $setiapSaat, $sertaMerta],

                    // 🎨 WARNA WARNI: Setiap kategori PPID memiliki warna identitas masing-masing
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.85)',  // Amber/Kuning untuk Berkala
                        'rgba(59, 130, 246, 0.85)',  // Blue/Biru untuk Setiap Saat
                        'rgba(16, 185, 129, 0.85)',  // Emerald/Hijau untuk Serta Merta
                    ],
                    'borderColor' => [
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                    ],
                    'borderWidth' => 1.5,
                ],
            ],
            'labels' => [
                '📅 Informasi Berkala',
                '🕒 Informasi Setiap Saat',
                '🚨 Informasi Serta Merta',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
