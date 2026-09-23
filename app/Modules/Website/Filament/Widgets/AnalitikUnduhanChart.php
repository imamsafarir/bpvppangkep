<?php

namespace App\Modules\Website\Filament\Widgets;

use App\Modules\Website\Models\InformasiPublik;
use App\Modules\Website\Models\Jdih;
use Filament\Widgets\ChartWidget;

class AnalitikUnduhanChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->role !== 'shortlink';
    }

    // Properti non-static sesuai standar resmi Filament
    protected ?string $heading = '📊 Statistik Akumulasi Unduhan Berkas';

    // Mengatur agar posisinya berada di baris setelah widget Stats Overview
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        // 1. Hitung total akumulasi unduhan berdasarkan kategori di Informasi Publik (PPID)
        $unduhanBerkala    = InformasiPublik::query()->where('kategori', 'berkala')->sum('jumlah_diunduh') ?? 0;
        $unduhanSetiapSaat = InformasiPublik::query()->where('kategori', 'setiap_saat')->sum('jumlah_diunduh') ?? 0;
        $unduhanSertaMerta = InformasiPublik::query()->where('kategori', 'serta_merta')->sum('jumlah_diunduh') ?? 0;

        // 2. Hitung total akumulasi unduhan berdasarkan status di Produk Hukum JDIH
        $unduhanJdihBerlaku = Jdih::query()->where('status_peraturan', 'berlaku')->sum('jumlah_diunduh') ?? 0;
        $unduhanJdihDicabut = Jdih::query()->where('status_peraturan', 'tidak_berlaku')->sum('jumlah_diunduh') ?? 0;

        return [
            'datasets' => [
                [
                    'label' => 'Total Diunduh (Kali)',
                    'data' => [
                        $unduhanBerkala,
                        $unduhanSetiapSaat,
                        $unduhanSertaMerta,
                        $unduhanJdihBerlaku,
                        $unduhanJdihDicabut
                    ],
                    // 🎨 Warna warni tiang grafik yang modern dan kontras
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.8)',  // Amber (Berkala)
                        'rgba(59, 130, 246, 0.8)',  // Blue (Setiap Saat)
                        'rgba(16, 185, 129, 0.8)',  // Emerald (Serta Merta)
                        'rgba(99, 102, 241, 0.8)',  // Indigo (JDIH Berlaku)
                        'rgba(239, 68, 68, 0.8)',   // Rose (JDIH Dicabut)
                    ],
                    'borderColor' => [
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(99, 102, 241)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                'PPID Berkala',
                'PPID Setiap Saat',
                'PPID Serta Merta',
                'JDIH Berlaku',
                'JDIH Dicabut',
            ],
        ];
    }

    protected function getType(): string
    {
        // Menggunakan tipe 'bar' (Grafik Batang) agar perbandingan jumlah unduhan terlihat jelas
        return 'bar';
    }
}
