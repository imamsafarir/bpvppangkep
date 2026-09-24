<?php

namespace App\Modules\TimSosmed\Filament\Pages;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Filament\Widgets\BebanKerjaOverview;
use App\Modules\TimSosmed\Models\Content;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    protected string|\Filament\Support\Enums\Width|null $maxContentWidth = 'full';

    protected string $view = 'timsosmed::filament.pages.calendar-page';

    protected static string | \UnitEnum | null $navigationGroup = 'Tim Media Sosial';

    protected static ?string $navigationLabel = 'Kalender Konten';

    protected static ?int $navigationSort = 1;

    protected ?string $heading = '📅 Kalender Jadwal & Manajemen Konten';

    public static function canAccess(): bool
    {
        return Auth::user()?->isMedsosTeam() ?? false;
    }

    /**
     * Widget Beban Kerja di atas kalender
     */
    protected function getHeaderWidgets(): array
    {
        return [
            BebanKerjaOverview::class,
        ];
    }

    /**
     * Data event kalender
     */
    public function getCalendarEvents(): array
    {
        return Content::with(['instruktur', 'pegawai', 'planner', 'editor', 'admin', 'platforms'])
            ->get()
            ->map(function ($content) {
                $statusColor = match ($content->status) {
                    'draft' => '#64748b',
                    'menunggu_editor' => '#f59e0b',
                    'revisi_editor', 'revisi_planner' => '#ef4444',
                    'siap_publish' => '#3b82f6',
                    'selesai' => '#10b981',
                    default => '#6b7280',
                };

                $statusLabel = match ($content->status) {
                    'draft' => 'Draft',
                    'menunggu_editor' => 'Menunggu Edit',
                    'revisi_editor' => 'Revisi Editor',
                    'revisi_planner' => 'Revisi Planner',
                    'siap_publish' => 'Siap Publish',
                    'selesai' => 'Selesai / Live',
                    default => ucfirst($content->status),
                };

                // Cari konseptor / pembuat draft awal
                $creator = $content->instruktur ?? $content->pegawai ?? $content->planner;
                $creatorRole = 'Pengusul';
                $creatorIcon = '💼';

                if ($content->instruktur_id) {
                    $creatorRole = 'Instruktur';
                    $creatorIcon = '👨‍🏫';
                } elseif ($content->planner_id) {
                    $creatorRole = 'Planner';
                    $creatorIcon = '📋';
                } elseif ($creator && $creator->isAdmin()) {
                    $creatorRole = 'Administrator';
                    $creatorIcon = '👑';
                } elseif ($creator && $creator->isInstruktur()) {
                    $creatorRole = 'Instruktur';
                    $creatorIcon = '👨‍🏫';
                } elseif ($creator && $creator->isMedsosPlanner()) {
                    $creatorRole = 'Planner';
                    $creatorIcon = '📋';
                }

                // Identifikasi siapa yang sedang memproses saat ini sesuai tahapan alur kerja
                $petugas = match ($content->status) {
                    'draft' => [
                        'role' => $creatorRole,
                        'name' => $creator?->name ?? 'Belum ada',
                        'icon' => $creatorIcon,
                    ],
                    'menunggu_editor' => [
                        'role' => 'Editor',
                        'name' => $content->editor?->name ?? 'Menunggu Penugasan Editor',
                        'icon' => '🎨',
                    ],
                    'revisi_editor' => [
                        'role' => 'Editor (Revisi)',
                        'name' => $content->editor?->name ?? 'Medsos Editor',
                        'icon' => '🎨',
                    ],
                    'revisi_planner' => [
                        'role' => 'Planner (Revisi)',
                        'name' => $content->planner?->name ?? 'Medsos Planner',
                        'icon' => '📋',
                    ],
                    'siap_publish' => [
                        'role' => 'Admin Platform',
                        'name' => $content->admin?->name ?? 'Medsos Admin Platform',
                        'icon' => '🚀',
                    ],
                    'selesai' => [
                        'role' => 'Admin Publikasi',
                        'name' => $content->admin?->name ?? ($content->planner?->name ?? 'Admin Medsos'),
                        'icon' => '✅',
                    ],
                    default => [
                        'role' => 'Petugas',
                        'name' => $content->planner?->name ?? '-',
                        'icon' => '👤',
                    ],
                };

                $konseptor = $creator?->name ?? null;

                return [
                    'id' => (string) $content->id,
                    'title' => $content->nama_kegiatan,
                    'start' => Carbon::parse($content->tanggal_kegiatan)->format('Y-m-d'),
                    'url' => ContentResource::getUrl('edit', ['record' => $content->id]),
                    'color' => $statusColor,
                    'extendedProps' => [
                        'status' => $content->status,
                        'status_label' => $statusLabel,
                        'status_color' => $statusColor,
                        'jenis_konten' => $content->jenis_konten === 'final' ? 'Final' : 'Bahan',
                        'platforms' => $content->platforms->pluck('name')->toArray(),
                        'petugas_role' => $petugas['role'],
                        'petugas_name' => $petugas['name'],
                        'petugas_icon' => $petugas['icon'],
                        'konseptor' => $konseptor,
                        'planner' => $content->planner ? $content->planner->name : '-',
                        'editor' => $content->editor ? $content->editor->name : '-',
                        'admin' => $content->admin ? $content->admin->name : '-',
                        'edit_url' => ContentResource::getUrl('edit', ['record' => $content->id]),
                    ],
                ];
            })
            ->toArray();
    }

    protected function getViewData(): array
    {
        return [
            'events' => json_encode($this->getCalendarEvents()),
        ];
    }
}
