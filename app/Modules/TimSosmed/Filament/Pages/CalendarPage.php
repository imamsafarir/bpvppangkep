<?php

namespace App\Modules\TimSosmed\Filament\Pages;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Filament\Widgets\BebanKerjaOverview;
use App\Modules\TimSosmed\Models\Comment;
use App\Modules\TimSosmed\Models\Content;
use App\Modules\TimSosmed\Models\ContentRead;
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

    public ?string $lastVersionHash = null;

    /**
     * Data event kalender dengan informasi diskusi & status belum dibaca
     */
    public function getCalendarEvents(): array
    {
        $userId = Auth::id();

        // Ambil waktu terakhir user membaca diskusi pada setiap konten
        $reads = ContentRead::where('user_id', $userId)
            ->pluck('last_read_at', 'content_id')
            ->toArray();

        return Content::with(['instruktur', 'pegawai', 'planner', 'editor', 'admin', 'platforms', 'comments'])
            ->get()
            ->map(function ($content) use ($userId, $reads) {
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

                // Perhitungan Komentar Diskusi & Status Belum Dibaca (Unread)
                $comments = $content->comments;
                $commentsCount = $comments->count();
                $unreadCount = 0;

                if ($commentsCount > 0) {
                    $lastReadAt = isset($reads[$content->id]) ? Carbon::parse($reads[$content->id]) : null;

                    if ($lastReadAt) {
                        // Komentar baru dari pengguna lain yang masuk setelah waktu baca terakhir
                        $unreadCount = $comments->where('user_id', '!=', $userId)
                            ->filter(fn($c) => $c->created_at > $lastReadAt)
                            ->count();
                    } else {
                        // Jika belum pernah dibuka sama sekali, semua komentar dari pengguna lain dianggap baru
                        $unreadCount = $comments->where('user_id', '!=', $userId)->count();
                    }
                }

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

                $canEdit = ContentResource::canEdit($content);
                $isSelesai = $content->status === 'selesai';
                $targetUrl = ($isSelesai || ! $canEdit)
                    ? ContentResource::getUrl('view', ['record' => $content->id])
                    : ContentResource::getUrl('edit', ['record' => $content->id]);
                $actionType = ($isSelesai || ! $canEdit) ? 'view' : 'edit';

                // Status Deadline: Muncul jika konten belum selesai dan tanggal_kegiatan <= hari ini
                $isDeadline = false;
                $isOverdue = false;
                $isToday = false;
                $deadlineLabel = null;
                $deadlineDays = 0;

                if (! $isSelesai && ! empty($content->tanggal_kegiatan)) {
                    $today = Carbon::today();
                    $targetDate = Carbon::parse($content->tanggal_kegiatan)->startOfDay();
                    $diffDays = (int) $today->diffInDays($targetDate, false);

                    if ($diffDays < 0) {
                        $isDeadline = true;
                        $isOverdue = true;
                        $deadlineDays = abs($diffDays);
                        $deadlineLabel = '⚠️ Lewat ' . $deadlineDays . ' hr';
                    } elseif ($diffDays === 0) {
                        $isDeadline = true;
                        $isToday = true;
                        $deadlineLabel = '🔥 Hari Ini';
                    }
                }

                return [
                    'id' => (string) $content->id,
                    'title' => $content->nama_kegiatan,
                    'start' => Carbon::parse($content->tanggal_kegiatan)->format('Y-m-d'),
                    'url' => $targetUrl,
                    'color' => $statusColor,
                    'extendedProps' => [
                        'status' => $content->status,
                        'status_label' => $statusLabel,
                        'status_color' => $statusColor,
                        'action_url' => $targetUrl,
                        'action_type' => $actionType,
                        'can_edit' => $canEdit,
                        'is_selesai' => $isSelesai,
                        'is_deadline' => $isDeadline,
                        'is_overdue' => $isOverdue,
                        'is_today' => $isToday,
                        'deadline_label' => $deadlineLabel,
                        'deadline_days' => $deadlineDays,
                        'jenis_konten' => $content->jenis_konten === 'final' ? 'Final' : 'Bahan',
                        'platforms' => $content->platforms->pluck('name')->toArray(),
                        'petugas_role' => $petugas['role'],
                        'petugas_name' => $petugas['name'],
                        'petugas_icon' => $petugas['icon'],
                        'konseptor' => $konseptor,
                        'planner' => $content->planner ? $content->planner->name : '-',
                        'editor' => $content->editor ? $content->editor->name : '-',
                        'admin' => $content->admin ? $content->admin->name : '-',
                        'edit_url' => $targetUrl,
                        'comments_count' => $commentsCount,
                        'unread_comments_count' => $unreadCount,
                        'has_unread_comments' => $unreadCount > 0,
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Tandai diskusi konten ini sudah dibaca oleh user saat membuka popup
     */
    public function markAsRead(int $contentId): void
    {
        if (Auth::check()) {
            ContentRead::updateOrCreate(
                [
                    'content_id' => $contentId,
                    'user_id' => Auth::id(),
                ],
                [
                    'last_read_at' => now(),
                ]
            );

            $this->dispatch('calendar-refresh');
        }
    }

    /**
     * Polling otomatis latar belakang (ultra cepat < 1ms):
     * Cek jika ada perubahan konten atau komentar baru tanpa perlu refresh browser
     */
    public function checkCalendarUpdates(): void
    {
        $latestContent = Content::max('updated_at') ?? '0';
        $latestComment = Comment::max('created_at') ?? '0';
        $currentHash = md5($latestContent . '_' . $latestComment);

        if ($this->lastVersionHash !== null && $this->lastVersionHash !== $currentHash) {
            $this->dispatch('calendar-refresh');
        }

        $this->lastVersionHash = $currentHash;
    }

    protected function getViewData(): array
    {
        return [
            'events' => json_encode($this->getCalendarEvents()),
        ];
    }
}
