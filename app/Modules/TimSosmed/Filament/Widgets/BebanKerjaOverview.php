<?php

namespace App\Modules\TimSosmed\Filament\Widgets;

use App\Modules\TimSosmed\Models\Content;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString; // <-- WAJIB IMPORT INI

class BebanKerjaOverview extends BaseWidget
{
    protected ?string $pollingInterval = '5s';

    public static function canView(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isMedsosTeam() ?? false;
    }

    protected function getStats(): array
    {
        // 1. Hitung beban Planner (Pisahkan Bahan & Final)
        $plannerBebanBahan = Content::query()
            ->whereIn('status', ['draft', 'revisi_planner'])
            ->where('jenis_konten', 'bahan')
            ->count('id'); // <-- Tambahkan 'id' agar Intelephense diam

        $plannerBebanFinal = Content::query()
            ->whereIn('status', ['draft', 'revisi_planner'])
            ->where('jenis_konten', 'final')
            ->count('id');

        // Total beban Planner adalah gabungan keduanya
        $plannerBeban = $plannerBebanBahan + $plannerBebanFinal;

        // 2. Hitung beban Editor & Admin
        $editorBeban = Content::query()
            ->whereIn('status', ['menunggu_editor', 'revisi_editor'])
            ->count('id');

        $adminBeban = Content::query()
            ->where('status', 'siap_publish')
            ->count('id');

        // --- TRIK SAKTI: INJEKSI CSS MANUAL (BYPASS TAILWIND JIT) ---
        $cssInject = "
            <style>
                /* Kotak Hijau (Aman) */
                .kotak-aman { background-color: #ecfdf5 !important; border: 1px solid #a7f3d0 !important; transition: all 0.3s ease !important; }
                /* Kotak Merah (Bahaya/Ada Tugas) */
                .kotak-bahaya { background-color: #fff1f2 !important; border: 1px solid #fecdd3 !important; transition: all 0.3s ease !important; }

                /* Penyesuaian Otomatis Untuk Dark Mode Filament */
                .dark .kotak-aman { background-color: rgba(16, 185, 129, 0.1) !important; border-color: rgba(16, 185, 129, 0.2) !important; }
                .dark .kotak-bahaya { background-color: rgba(244, 63, 94, 0.15) !important; border-color: rgba(244, 63, 94, 0.2) !important; }

                /* Efek Animasi Melayang Saat Mouse Diarahkan (Hover) */
                .kotak-hover:hover { transform: translateY(-4px) !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
            </style>
        ";

        // Susun teks untuk Planner, dan sisipkan CSS ke widget pertama agar ikut di-render oleh browser
        $plannerText = $plannerBeban === 0
            ? 'Semua brief beres! ✨'
            : "Bahan: {$plannerBebanBahan} | Final: {$plannerBebanFinal} (Perlu dikerjakan)";

        $plannerDescWithCss = new HtmlString($cssInject . $plannerText);

        return [
            // STATS 1: PLANNER & INSTRUKTUR
            Stat::make('Antrean Planner / Instruktur / Pegawai', $plannerBeban . ' Konten')
                ->description($plannerDescWithCss) // <-- CSS dipasang di sini
                ->descriptionIcon($plannerBeban === 0 ? 'heroicon-m-check-circle' : 'heroicon-m-pencil-square')
                ->chart($plannerBeban === 0 ? [0, 0, 0] : [7, 4, 6, 8, 5, 3, $plannerBeban])
                ->color($plannerBeban === 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => $plannerBeban === 0 ? 'kotak-aman kotak-hover' : 'kotak-bahaya kotak-hover',
                ]),

            // STATS 2: EDITOR
            Stat::make('Antrean Editor', $editorBeban . ' Konten')
                ->description($editorBeban === 0 ? 'Editor bersih dari antrean! 🎬' : 'Menunggu proses editing video')
                ->descriptionIcon($editorBeban === 0 ? 'heroicon-m-check-circle' : 'heroicon-m-scissors')
                ->chart($editorBeban === 0 ? [0, 0, 0] : [2, 5, 10, 3, 15, 4, $editorBeban])
                ->color($editorBeban === 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => $editorBeban === 0 ? 'kotak-aman kotak-hover' : 'kotak-bahaya kotak-hover',
                ]),

            // STATS 3: ADMIN
            Stat::make('Antrean Admin (Siap Publish)', $adminBeban . ' Konten')
                ->description($adminBeban === 0 ? 'Semua konten sudah tayang! 🚀' : 'Menunggu posting ke media sosial')
                ->descriptionIcon($adminBeban === 0 ? 'heroicon-m-check-circle' : 'heroicon-m-globe-alt')
                ->chart($adminBeban === 0 ? [0, 0, 0] : [1, 2, 1, 4, 3, 5, $adminBeban])
                ->color($adminBeban === 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => $adminBeban === 0 ? 'kotak-aman kotak-hover' : 'kotak-bahaya kotak-hover',
                ]),
        ];
    }
}
