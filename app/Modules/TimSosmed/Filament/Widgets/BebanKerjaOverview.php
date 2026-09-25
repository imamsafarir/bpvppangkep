<?php

namespace App\Modules\TimSosmed\Filament\Widgets;

use App\Modules\TimSosmed\Models\Content;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class BebanKerjaOverview extends BaseWidget
{
    protected ?string $pollingInterval = '5s';

    protected array | int | null $columns = 3;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user ? ($user->isMedsosTeam() || $user->isAdmin() || $user->isStaff()) : false;
    }

    protected function getStats(): array
    {
        $today = Carbon::today()->toDateString();

        // 1. TAHAP 1: PLANNER & KONSEPTOR (Draft & Revisi Planner)
        $plannerBebanBahan = Content::query()
            ->whereIn('status', ['draft', 'revisi_planner'])
            ->where('jenis_konten', 'bahan')
            ->count('id');

        $plannerBebanFinal = Content::query()
            ->whereIn('status', ['draft', 'revisi_planner'])
            ->where('jenis_konten', 'final')
            ->count('id');

        $plannerBeban = $plannerBebanBahan + $plannerBebanFinal;

        $plannerOverdue = Content::query()
            ->whereIn('status', ['draft', 'revisi_planner'])
            ->whereNotNull('tanggal_kegiatan')
            ->where('tanggal_kegiatan', '<', $today)
            ->count('id');

        $plannerDueToday = Content::query()
            ->whereIn('status', ['draft', 'revisi_planner'])
            ->whereNotNull('tanggal_kegiatan')
            ->where('tanggal_kegiatan', $today)
            ->count('id');

        // 2. TAHAP 2: EDITOR (Menunggu Editor & Revisi Editor)
        $editorBeban = Content::query()
            ->whereIn('status', ['menunggu_editor', 'revisi_editor'])
            ->count('id');

        $editorOverdue = Content::query()
            ->whereIn('status', ['menunggu_editor', 'revisi_editor'])
            ->whereNotNull('tanggal_kegiatan')
            ->where('tanggal_kegiatan', '<', $today)
            ->count('id');

        $editorDueToday = Content::query()
            ->whereIn('status', ['menunggu_editor', 'revisi_editor'])
            ->whereNotNull('tanggal_kegiatan')
            ->where('tanggal_kegiatan', $today)
            ->count('id');

        // 3. TAHAP 3: ADMIN PUBLIKASI (Siap Publish)
        $adminBeban = Content::query()
            ->where('status', 'siap_publish')
            ->count('id');

        $adminOverdue = Content::query()
            ->where('status', 'siap_publish')
            ->whereNotNull('tanggal_kegiatan')
            ->where('tanggal_kegiatan', '<', $today)
            ->count('id');

        $adminDueToday = Content::query()
            ->where('status', 'siap_publish')
            ->whereNotNull('tanggal_kegiatan')
            ->where('tanggal_kegiatan', $today)
            ->count('id');

        // --- STYLING MODERN (LIGHT & DARK MODE, ELEGAN, PULSING DEADLINE) ---
        $cssInject = "
            <style>
                /* Kartu Modern dengan Transisi & Efek Angkat */
                .kotak-hover {
                    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                    border-radius: 14px !important;
                    position: relative !important;
                    overflow: hidden !important;
                }
                .kotak-hover:hover {
                    transform: translateY(-3px) !important;
                    box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04) !important;
                }

                /* 1. KOTAK KRITIS / LEWAT DEADLINE (PULSING MERAH MENYALA) */
                .kotak-kritis-pulse {
                    background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%) !important;
                    border: 1.5px solid #f43f5e !important;
                    box-shadow: 0 0 0 1px rgba(244, 63, 94, 0.2) !important;
                    animation: vc-pulse-alarm 2.2s infinite !important;
                }
                @keyframes vc-pulse-alarm {
                    0% { box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.45); }
                    70% { box-shadow: 0 0 0 8px rgba(244, 63, 94, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(244, 63, 94, 0); }
                }
                .dark .kotak-kritis-pulse {
                    background: linear-gradient(135deg, rgba(225, 29, 72, 0.22) 0%, rgba(159, 18, 57, 0.3) 100%) !important;
                    border-color: rgba(244, 63, 94, 0.55) !important;
                }

                /* 2. KOTAK PERINGATAN DEADLINE HARI INI (AMBER WARNING) */
                .kotak-warning {
                    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
                    border: 1.5px solid #f59e0b !important;
                }
                .dark .kotak-warning {
                    background: linear-gradient(135deg, rgba(245, 158, 11, 0.18) 0%, rgba(180, 83, 9, 0.25) 100%) !important;
                    border-color: rgba(245, 158, 11, 0.45) !important;
                }

                /* 3. KOTAK AMAN / SELESAI / SESUAI JADWAL (HIJAU EMERALD) */
                .kotak-aman {
                    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%) !important;
                    border: 1px solid #a7f3d0 !important;
                }
                .dark .kotak-aman {
                    background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(5, 150, 105, 0.18) 100%) !important;
                    border-color: rgba(16, 185, 129, 0.3) !important;
                }

                /* 4. KOTAK NORMAL PEKERJAAN (BERSIH & ELEGAN) */
                .kotak-normal-role {
                    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
                    border: 1px solid #e2e8f0 !important;
                }
                .dark .kotak-normal-role {
                    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
                    border-color: #334155 !important;
                }
            </style>
        ";

        // --- KARTU 1: TAHAP PLANNER & KONSEPTOR ---
        if ($plannerOverdue > 0) {
            $plannerTitle = "📋 1. Perencanaan (🚨 {$plannerOverdue} Lewat Deadline)";
            $plannerDesc = "⚠️ {$plannerOverdue} konsep lewat deadline! • Bahan: {$plannerBebanBahan}, Final: {$plannerBebanFinal}";
            $plannerIcon = 'heroicon-m-exclamation-triangle';
            $plannerColor = 'danger';
            $plannerClass = 'kotak-kritis-pulse kotak-hover';
            $plannerChart = [3, 5, 4, 7, 6, 8, $plannerBeban + 4];
        } elseif ($plannerDueToday > 0) {
            $plannerTitle = "📋 1. Perencanaan (🔥 {$plannerDueToday} Deadline Hari Ini)";
            $plannerDesc = "🔥 {$plannerDueToday} jatuh tempo hari ini • Bahan: {$plannerBebanBahan}, Final: {$plannerBebanFinal}";
            $plannerIcon = 'heroicon-m-fire';
            $plannerColor = 'warning';
            $plannerClass = 'kotak-warning kotak-hover';
            $plannerChart = [2, 3, 2, 4, 3, 5, $plannerBeban + 2];
        } elseif ($plannerBeban === 0) {
            $plannerTitle = '📋 1. Perencanaan (Planner & Pengusul)';
            $plannerDesc = 'Semua konsep & brief beres! ✨';
            $plannerIcon = 'heroicon-m-check-circle';
            $plannerColor = 'success';
            $plannerClass = 'kotak-aman kotak-hover';
            $plannerChart = [0, 0, 0];
        } else {
            $plannerTitle = '📋 1. Perencanaan (Planner & Pengusul)';
            $plannerDesc = "Bahan: {$plannerBebanBahan} | Final: {$plannerBebanFinal} (Tepat waktu ✨)";
            $plannerIcon = 'heroicon-m-pencil-square';
            $plannerColor = 'warning';
            $plannerClass = 'kotak-normal-role kotak-hover';
            $plannerChart = [2, 4, 3, 5, 4, 6, $plannerBeban];
        }

        // Injeksi CSS pada deskripsi kartu pertama
        $plannerDescWithCss = new HtmlString($cssInject . $plannerDesc);

        // --- KARTU 2: TAHAP EDITOR (PRODUKSI) ---
        if ($editorOverdue > 0) {
            $editorTitle = "🎨 2. Produksi Editor (🚨 {$editorOverdue} Lewat Deadline)";
            $editorDesc = "⚠️ {$editorOverdue} konten editing melewati batas deadline!";
            $editorIcon = 'heroicon-m-exclamation-triangle';
            $editorColor = 'danger';
            $editorClass = 'kotak-kritis-pulse kotak-hover';
            $editorChart = [2, 4, 5, 4, 7, 8, $editorBeban + 4];
        } elseif ($editorDueToday > 0) {
            $editorTitle = "🎨 2. Produksi Editor (🔥 {$editorDueToday} Hari Ini)";
            $editorDesc = "🔥 {$editorDueToday} konten editing jatuh tempo hari ini";
            $editorIcon = 'heroicon-m-fire';
            $editorColor = 'warning';
            $editorClass = 'kotak-warning kotak-hover';
            $editorChart = [1, 3, 2, 4, 3, 5, $editorBeban + 2];
        } elseif ($editorBeban === 0) {
            $editorTitle = '🎨 2. Produksi Konten (Editor)';
            $editorDesc = 'Editor bersih dari antrean! 🎬';
            $editorIcon = 'heroicon-m-check-circle';
            $editorColor = 'success';
            $editorClass = 'kotak-aman kotak-hover';
            $editorChart = [0, 0, 0];
        } else {
            $editorTitle = '🎨 2. Produksi Konten (Editor)';
            $editorDesc = 'Menunggu video editing & desain visual (Tepat waktu ✨)';
            $editorIcon = 'heroicon-m-scissors';
            $editorColor = 'info';
            $editorClass = 'kotak-normal-role kotak-hover';
            $editorChart = [1, 3, 2, 4, 3, 5, $editorBeban];
        }

        // --- KARTU 3: TAHAP ADMIN (PUBLIKASI) ---
        if ($adminOverdue > 0) {
            $adminTitle = "🚀 3. Publikasi Medsos (🚨 {$adminOverdue} Lewat Jadwal)";
            $adminDesc = "⚠️ {$adminOverdue} konten siap posting melewati jadwal tayang!";
            $adminIcon = 'heroicon-m-exclamation-triangle';
            $adminColor = 'danger';
            $adminClass = 'kotak-kritis-pulse kotak-hover';
            $adminChart = [1, 3, 2, 5, 4, 6, $adminBeban + 3];
        } elseif ($adminDueToday > 0) {
            $adminTitle = "🚀 3. Publikasi Medsos (🔥 {$adminDueToday} Hari Ini)";
            $adminDesc = "🔥 {$adminDueToday} konten siap posting hari ini";
            $adminIcon = 'heroicon-m-fire';
            $adminColor = 'warning';
            $adminClass = 'kotak-warning kotak-hover';
            $adminChart = [1, 2, 2, 3, 2, 4, $adminBeban + 1];
        } elseif ($adminBeban === 0) {
            $adminTitle = '🚀 3. Siap Tayang (Admin Platform)';
            $adminDesc = 'Semua materi sudah diposting live! 🚀';
            $adminIcon = 'heroicon-m-check-circle';
            $adminColor = 'success';
            $adminClass = 'kotak-aman kotak-hover';
            $adminChart = [0, 0, 0];
        } else {
            $adminTitle = '🚀 3. Siap Tayang (Admin Platform)';
            $adminDesc = 'Materi siap, menunggu tayang di media sosial (Tepat waktu ✨)';
            $adminIcon = 'heroicon-m-globe-alt';
            $adminColor = 'primary';
            $adminClass = 'kotak-normal-role kotak-hover';
            $adminChart = [1, 2, 1, 3, 2, 4, $adminBeban];
        }

        return [
            // STATS 1: PERENCANAAN
            Stat::make($plannerTitle, $plannerBeban . ' Konten')
                ->description($plannerDescWithCss)
                ->descriptionIcon($plannerIcon)
                ->chart($plannerChart)
                ->color($plannerColor)
                ->extraAttributes([
                    'class' => $plannerClass,
                ]),

            // STATS 2: PRODUKSI EDITOR
            Stat::make($editorTitle, $editorBeban . ' Konten')
                ->description($editorDesc)
                ->descriptionIcon($editorIcon)
                ->chart($editorChart)
                ->color($editorColor)
                ->extraAttributes([
                    'class' => $editorClass,
                ]),

            // STATS 3: PUBLIKASI ADMIN
            Stat::make($adminTitle, $adminBeban . ' Konten')
                ->description($adminDesc)
                ->descriptionIcon($adminIcon)
                ->chart($adminChart)
                ->color($adminColor)
                ->extraAttributes([
                    'class' => $adminClass,
                ]),
        ];
    }
}
