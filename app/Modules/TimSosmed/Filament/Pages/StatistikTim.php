<?php

namespace App\Modules\TimSosmed\Filament\Pages;

use App\Models\User;
use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Filament\Widgets\TimSosmedStatsOverview;
use App\Modules\TimSosmed\Models\Content;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class StatistikTim extends Page implements HasTable
{
    use InteractsWithTable;
    protected string|\Filament\Support\Enums\Width|null $maxContentWidth = 'full';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static string|\UnitEnum|null $navigationGroup = 'Tim Media Sosial';
    protected static ?string $navigationLabel = 'Statistik Tim';
    protected static ?int $navigationSort = 3;

    protected string $view = 'timsosmed::filament.pages.statistik-tim';

    protected ?string $heading = '📊 Statistik & Performa Tim Medsos';
    protected ?string $subheading = 'Pantau kontribusi kerja real-time, peringkat aktivitas, dan beban tugas seluruh anggota tim sosial media.';

    public static function canAccess(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isMedsosTeam() ?? false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TimSosmedStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_content')
                ->label('✨ Buat Konten Baru')
                ->color('primary')
                ->url(fn() => ContentResource::getUrl('create')),

            Action::make('kalender')
                ->label('📅 Kalender Konten')
                ->color('gray')
                ->url(fn() => CalendarPage::getUrl()),

            Action::make('daftar_konten')
                ->label('📋 Daftar Konten')
                ->color('gray')
                ->url(fn() => ContentResource::getUrl('index')),

            // Tombol sinkronisasi folder media: hanya terlihat oleh SuperAdmin
            Action::make('migrate_media')
                ->label('🔄 Sinkronisasi Folder Media')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn() => auth()->user()?->isSuperAdmin() ?? false)
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Folder Media TimSosmed')
                ->modalDescription('Proses ini akan memindahkan semua file media dari folder content/ ke timsosmed/content/ yang lebih rapi. Lakukan sekali saja setelah deployment pertama. Lanjutkan?')
                ->modalSubmitActionLabel('Ya, Sinkronisasi Sekarang')
                ->action(function () {
                    try {
                        \Illuminate\Support\Facades\Artisan::call('timsosmed:migrate-media');

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Sinkronisasi Selesai')
                            ->body('Semua file media TimSosmed berhasil dipindahkan ke folder timsosmed/content/.')
                            ->send();
                    } catch (\Throwable $e) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Sinkronisasi Gagal')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
        ];
    }

    public function mount(): void
    {
        $this->tableFilters['periode_pengerjaan'] = [
            'value' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY USER TIM MEDSOS
    |--------------------------------------------------------------------------
    | Membaca user yang memiliki peran tim sosial media.
    | Akun Superadmin / Administrator sistem disembunyikan dari statistik performa tim.
    */
    protected function teamUserQuery(): Builder
    {
        return User::medsosTeam()
            ->where(function (Builder $query) {
                $query->whereDoesntHave('roles', function (Builder $q) {
                    $q->whereIn('name', ['super_admin', 'admin']);
                })
                    ->where('users.username', '!=', 'superadmin')
                    ->where('users.email', '!=', 'superadmin@admin.com')
                    ->where('users.role', 'not like', '%super_admin%')
                    ->where('users.role', 'not like', '%superadmin%')
                    ->where('users.role', '!=', 'admin');
            });
    }

    /*
    |--------------------------------------------------------------------------
    | RANGE FILTER TABEL
    |--------------------------------------------------------------------------
    */
    protected function getSelectedPeriodRange(): array
    {
        $periode = data_get($this->tableFilters, 'periode_pengerjaan.value');

        if (is_array($periode)) {
            $periode = data_get($periode, 'value');
        }

        $start = null;
        $end = null;

        if ($periode === 'this_month') {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        }

        if ($periode === 'last_month') {
            $start = now()->subMonthNoOverflow()->startOfMonth();
            $end = now()->subMonthNoOverflow()->endOfMonth();
        }

        if ($periode === 'this_year') {
            $start = now()->startOfYear();
            $end = now()->endOfYear();
        }

        return [$start, $end];
    }

    /*
    |--------------------------------------------------------------------------
    | BASE QUERY CONTENT BERDASARKAN RANGE
    |--------------------------------------------------------------------------
    */
    protected function contentQueryByRange($start = null, $end = null): Builder
    {
        return Content::query()
            ->when(
                $start && $end,
                fn(Builder $query) => $query->whereBetween('updated_at', [$start, $end])
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SUBQUERY HITUNG KONTEN USER
    |--------------------------------------------------------------------------
    */
    protected function countUserContentSubQuery(
        $start = null,
        $end = null,
        ?string $jenisKonten = null,
        ?array $statusIn = null,
        ?array $statusNotIn = null
    ): Builder {
        return $this->contentQueryByRange($start, $end)
            ->selectRaw('COUNT(*)')
            ->where(function (Builder $query) {
                $query
                    ->whereColumn('contents.pegawai_id', 'users.id')
                    ->orWhereColumn('contents.instruktur_id', 'users.id')
                    ->orWhereColumn('contents.planner_id', 'users.id');
            })
            ->when(
                $jenisKonten,
                fn(Builder $query) => $query->where('jenis_konten', $jenisKonten)
            )
            ->when(
                $statusIn,
                fn(Builder $query) => $query->whereIn('status', $statusIn)
            )
            ->when(
                $statusNotIn,
                fn(Builder $query) => $query->whereNotIn('status', $statusNotIn)
            );
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY USER DENGAN HITUNGAN STATISTIK
    |--------------------------------------------------------------------------
    */
    protected function statisticUserQuery($start = null, $end = null): Builder
    {
        $roleSortSql = "
            COALESCE(
                (SELECT MIN(r.name) FROM model_has_roles mhr JOIN roles r ON r.id = mhr.role_id WHERE mhr.model_id = users.id AND mhr.model_type = 'App\\\\Models\\\\User'),
                users.role
            )
        ";

        $editorRoleCountSql = "
            (
                CASE
                    WHEN users.role LIKE '%editor%' OR users.role = 'admin' OR users.role = 'super_admin' THEN 1
                    WHEN EXISTS (
                        SELECT 1 FROM model_has_roles mhr
                        JOIN roles r ON r.id = mhr.role_id
                        WHERE mhr.model_id = users.id
                        AND mhr.model_type = 'App\\\\Models\\\\User'
                        AND r.name IN ('editor', 'medsos_editor', 'super_admin', 'admin')
                    ) THEN 1
                    ELSE 0
                END
            )
        ";

        $adminRoleCountSql = "
            (
                CASE
                    WHEN users.role LIKE '%admin_platform%' OR users.role = 'admin' OR users.role = 'super_admin' THEN 1
                    WHEN EXISTS (
                        SELECT 1 FROM model_has_roles mhr
                        JOIN roles r ON r.id = mhr.role_id
                        WHERE mhr.model_id = users.id
                        AND mhr.model_type = 'App\\\\Models\\\\User'
                        AND r.name IN ('admin_platform', 'medsos_admin_platform', 'super_admin', 'admin')
                    ) THEN 1
                    ELSE 0
                END
            )
        ";

        return $this->teamUserQuery()
            ->with('roles')
            ->select('users.*')
            ->selectRaw("{$roleSortSql} as role_sort")
            ->selectRaw("{$editorRoleCountSql} as editor_role_count")
            ->selectRaw("{$adminRoleCountSql} as admin_role_count")

            ->selectSub(
                $this->countUserContentSubQuery(
                    start: $start,
                    end: $end,
                    jenisKonten: 'bahan',
                    statusNotIn: ['draft', 'revisi_planner']
                ),
                'bahan_count'
            )

            ->selectSub(
                $this->countUserContentSubQuery(
                    start: $start,
                    end: $end,
                    jenisKonten: 'final',
                    statusNotIn: ['draft', 'revisi_planner']
                ),
                'final_count'
            )

            ->selectSub(
                $this->countUserContentSubQuery(
                    start: $start,
                    end: $end,
                    statusIn: ['draft', 'revisi_planner']
                ),
                'active_content_count'
            )

            ->selectSub(
                $this->contentQueryByRange($start, $end)
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('contents.editor_id', 'users.id')
                    ->whereIn('status', ['siap_publish', 'selesai']),
                'editor_finished_count'
            )

            ->selectSub(
                $this->contentQueryByRange($start, $end)
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('contents.editor_id', 'users.id')
                    ->where('status', 'revisi_editor'),
                'editor_revision_count'
            )

            ->selectSub(
                $this->contentQueryByRange($start, $end)
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('contents.admin_id', 'users.id')
                    ->where('status', 'selesai'),
                'admin_finished_count'
            );
    }

    protected function totalContributionSortSql(): string
    {
        return "
            (
                COALESCE(bahan_count, 0) +
                COALESCE(final_count, 0) +
                COALESCE(editor_finished_count, 0) +
                COALESCE(admin_finished_count, 0)
            )
        ";
    }

    protected function workloadSortSql(int $sharedEditorQueueCount, int $sharedAdminQueueCount): string
    {
        return "
            (
                COALESCE(active_content_count, 0) +
                COALESCE(editor_revision_count, 0) +
                CASE
                    WHEN COALESCE(editor_role_count, 0) > 0 THEN {$sharedEditorQueueCount}
                    ELSE 0
                END +
                CASE
                    WHEN COALESCE(admin_role_count, 0) > 0 THEN {$sharedAdminQueueCount}
                    ELSE 0
                END
            )
        ";
    }

    protected function calculateTotalContribution(User $user): int
    {
        return
            (int) ($user->bahan_count ?? 0) +
            (int) ($user->final_count ?? 0) +
            (int) ($user->editor_finished_count ?? 0) +
            (int) ($user->admin_finished_count ?? 0);
    }

    protected function calculateWorkload(
        User $user,
        int $sharedEditorQueueCount,
        int $sharedAdminQueueCount
    ): int {
        $beban = 0;

        // Konten konsep/draft/revisi milik user sendiri
        $beban += (int) ($user->active_content_count ?? 0);

        // Jika memiliki tugas editor
        if ($user->isMedsosEditor()) {
            $beban += $sharedEditorQueueCount;
            $beban += (int) ($user->editor_revision_count ?? 0);
        }

        // Jika memiliki tugas admin platform
        if ($user->isMedsosAdminPlatform()) {
            $beban += $sharedAdminQueueCount;
        }

        return $beban;
    }

    public function table(Table $table): Table
    {
        [$start, $end] = $this->getSelectedPeriodRange();

        $sharedEditorQueueCount = $this->contentQueryByRange($start, $end)
            ->where('status', 'menunggu_editor')
            ->count();

        $sharedAdminQueueCount = $this->contentQueryByRange($start, $end)
            ->where('status', 'siap_publish')
            ->count();

        $totalContributionSortSql = $this->totalContributionSortSql();
        $workloadSortSql = $this->workloadSortSql($sharedEditorQueueCount, $sharedAdminQueueCount);

        return $table
            ->heading('Detail Statistik & Performa Seluruh Anggota Tim')
            ->description('Tabel rincian performa lengkap dan beban tugas aktif seluruh pengguna dengan peran tim media sosial.')
            ->query(
                $this->statisticUserQuery($start, $end)
            )
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('50px'),

                TextColumn::make('name')
                    ->label('Anggota Tim')
                    ->searchable(['name', 'username', 'email'])
                    ->sortable()
                    ->html()
                    ->state(function (User $record) {
                        $words = preg_split('/\s+/', trim($record->name));
                        $initials = count($words) >= 2
                            ? strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1))
                            : strtoupper(mb_substr($words[0] ?? '?', 0, 2));

                        $avatarBg = match (true) {
                            $record->isAdmin() => '#ef4444',
                            $record->isMedsosPlanner() => '#f59e0b',
                            $record->isMedsosEditor() => '#10b981',
                            $record->isMedsosAdminPlatform() => '#3b82f6',
                            $record->isInstruktur() => '#8b5cf6',
                            default => '#64748b',
                        };

                        $name = htmlspecialchars($record->name);
                        $username = htmlspecialchars($record->username ? '@' . $record->username : '');
                        $email = htmlspecialchars($record->email ?? '');

                        return new HtmlString("
                            <div style='display: flex; align-items: center; gap: 10px; text-align: left;'>
                                <div style='width: 32px; height: 32px; border-radius: 9999px; background: {$avatarBg}; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'>
                                    {$initials}
                                </div>
                                <div style='display: flex; flex-direction: column; min-width: 0;'>
                                    <span style='font-size: 13px; font-weight: 700; color: #1e293b; line-height: 1.2;'>
                                        {$name}
                                    </span>
                                    <span style='font-size: 11px; color: #64748b; margin-top: 2px;'>
                                        {$username}" . ($email ? " • {$email}" : "") . "
                                    </span>
                                </div>
                            </div>
                        ");
                    }),

                TextColumn::make('roles_display')
                    ->label('Peran Medsos')
                    ->html()
                    ->state(function (User $record) {
                        $badges = $record->medsos_role_badges;
                        if (empty($badges)) {
                            return new HtmlString("<span style='color: #94a3b8; font-size: 11px;'>-</span>");
                        }

                        $pills = [];
                        foreach ($badges as $b) {
                            $colorMap = [
                                'danger'  => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fecaca'],
                                'warning' => ['bg' => '#fef3c7', 'color' => '#b45309', 'border' => '#fde68a'],
                                'success' => ['bg' => '#dcfce7', 'color' => '#15803d', 'border' => '#bbf7d0'],
                                'info'    => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'border' => '#bfdbfe'],
                                'primary' => ['bg' => '#ede9fe', 'color' => '#6d28d9', 'border' => '#ddd6fe'],
                                'gray'    => ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#e2e8f0'],
                            ];
                            $c = $colorMap[$b['color'] ?? 'gray'] ?? $colorMap['gray'];
                            $pills[] = "<span style='font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 9999px; background: {$c['bg']}; color: {$c['color']}; border: 1px solid {$c['border']}; white-space: nowrap;'>{$b['icon']} {$b['label']}</span>";
                        }

                        return new HtmlString("<div style='display: flex; align-items: center; gap: 4px; flex-wrap: wrap;'>" . implode('', $pills) . "</div>");
                    })
                    ->sortable(
                        query: fn(Builder $query, string $direction): Builder =>
                        $query->orderBy('role_sort', $direction)
                    ),

                TextColumn::make('kontribusi_peran')
                    ->label('Rincian Kontribusi')
                    ->html()
                    ->state(function (User $record) {
                        $bahan = (int) ($record->bahan_count ?? 0);
                        $final = (int) ($record->final_count ?? 0);
                        $editor = (int) ($record->editor_finished_count ?? 0);
                        $admin = (int) ($record->admin_finished_count ?? 0);

                        $pills = [];
                        if ($bahan > 0) {
                            $pills[] = "<span title='Konten Bahan Mentah' style='font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 9999px; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;'>📦 {$bahan} Bahan</span>";
                        }
                        if ($final > 0) {
                            $pills[] = "<span title='Konten Final Siap Publish' style='font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 9999px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;'>✨ {$final} Final</span>";
                        }
                        if ($editor > 0) {
                            $pills[] = "<span title='Konten Selesai Diedit' style='font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 9999px; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;'>🎨 {$editor} Edit</span>";
                        }
                        if ($admin > 0) {
                            $pills[] = "<span title='Konten Ditayangkan Live' style='font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 9999px; background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe;'>🚀 {$admin} Tayang</span>";
                        }

                        if (empty($pills)) {
                            return new HtmlString("<span style='font-size: 11px; color: #94a3b8;'>-</span>");
                        }

                        return new HtmlString("<div style='display: flex; align-items: center; gap: 4px; flex-wrap: wrap;'>" . implode('', $pills) . "</div>");
                    })
                    ->sortable(
                        query: fn(Builder $query, string $direction): Builder =>
                        $query->orderByRaw("{$totalContributionSortSql} {$direction}")
                    ),

                TextColumn::make('beban_kerja')
                    ->label('Beban Tugas Aktif')
                    ->sortable(
                        query: fn(Builder $query, string $direction): Builder =>
                        $query->orderByRaw("{$workloadSortSql} {$direction}")
                    )
                    ->html()
                    ->state(function (User $record) use ($sharedEditorQueueCount, $sharedAdminQueueCount) {
                        $beban = $this->calculateWorkload($record, $sharedEditorQueueCount, $sharedAdminQueueCount);

                        if ($beban === 0) {
                            $bg = '#ecfdf5';
                            $color = '#047857';
                            $border = '#a7f3d0';
                            $dot = '#10b981';
                            $text = 'Senggang (0)';
                        } elseif ($beban <= 2) {
                            $bg = '#f0fdf4';
                            $color = '#15803d';
                            $border = '#bbf7d0';
                            $dot = '#22c55e';
                            $text = "{$beban} Tugas (Normal)";
                        } elseif ($beban <= 4) {
                            $bg = '#fffbeb';
                            $color = '#b45309';
                            $border = '#fde68a';
                            $dot = '#f59e0b';
                            $text = "{$beban} Tugas (Sibuk)";
                        } else {
                            $bg = '#fef2f2';
                            $color = '#b91c1c';
                            $border = '#fecaca';
                            $dot = '#ef4444';
                            $text = "{$beban} Tugas (Padat)";
                        }

                        return new HtmlString("
                            <span style='display: inline-flex; align-items: center; gap: 6px; font-size: 10.5px; font-weight: 700; padding: 2.5px 8px; border-radius: 9999px; background: {$bg}; color: {$color}; border: 1px solid {$border}; white-space: nowrap;'>
                                <span style='width: 6px; height: 6px; border-radius: 9999px; background: {$dot};'></span>
                                <span>{$text}</span>
                            </span>
                        ");
                    })
                    ->tooltip(function (User $record) use ($sharedEditorQueueCount, $sharedAdminQueueCount) {
                        $lines = [];
                        if ($record->active_content_count > 0) {
                            $lines[] = "• {$record->active_content_count} Konsep/Draft/Revisi milik sendiri";
                        }
                        if ($record->isMedsosEditor()) {
                            if ($sharedEditorQueueCount > 0) $lines[] = "• {$sharedEditorQueueCount} Antrean Menunggu Edit (Tim Editor)";
                            if ($record->editor_revision_count > 0) $lines[] = "• {$record->editor_revision_count} Revisi Editor";
                        }
                        if ($record->isMedsosAdminPlatform()) {
                            if ($sharedAdminQueueCount > 0) $lines[] = "• {$sharedAdminQueueCount} Siap Tayang / Publish (Tim Admin)";
                        }
                        return empty($lines) ? 'Tidak ada beban tugas aktif saat ini. Anggota siap menerima brief/tugas baru!' : "Rincian Tugas Aktif:\n" . implode("\n", $lines);
                    }),

                TextColumn::make('total_kontribusi')
                    ->label('Total Kontribusi')
                    ->sortable(
                        query: fn(Builder $query, string $direction): Builder =>
                        $query->orderByRaw("{$totalContributionSortSql} {$direction}")
                    )
                    ->html()
                    ->state(function (User $record) {
                        $total = $this->calculateTotalContribution($record);

                        if ($total === 0) {
                            return new HtmlString("<span style='font-size: 11px; color: #94a3b8; font-weight: 600;'>0 Kontribusi</span>");
                        }

                        return new HtmlString("
                            <div style='display: inline-flex; align-items: center; gap: 5px; padding: 2.5px 9px; border-radius: 9999px; background: #ecfdf5; border: 1px solid #a7f3d0;'>
                                <span style='font-size: 11px;'>🏆</span>
                                <span style='font-size: 12px; font-weight: 800; color: #047857;'>{$total}</span>
                                <span style='font-size: 9.5px; font-weight: 700; color: #059669;'>Kontribusi</span>
                            </div>
                        ");
                    }),
            ])
            ->filters([
                SelectFilter::make('periode_pengerjaan')
                    ->label('Periode Pengerjaan')
                    ->placeholder('Semua Periode')
                    ->options([
                        'this_month' => 'Bulan Ini',
                        'last_month' => 'Bulan Lalu',
                        'this_year' => 'Tahun Ini',
                    ])
                    ->query(fn($query) => $query),

                SelectFilter::make('role')
                    ->label('Filter Peran Medsos')
                    ->placeholder('Semua Peran')
                    ->options([
                        'planner'        => '📋 Medsos Planner',
                        'editor'         => '🎨 Medsos Editor',
                        'admin_platform' => '🚀 Medsos Admin Platform',
                        'instruktur'     => '👨‍🏫 Medsos Instruktur',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $role = $data['value'] ?? null;
                        if (! $role) return $query;

                        $matchRoles = match ($role) {
                            'planner'        => ['planner', 'medsos_planner'],
                            'editor'         => ['editor', 'medsos_editor'],
                            'admin_platform' => ['admin_platform', 'medsos_admin_platform'],
                            'instruktur'     => ['instruktur', 'medsos_instruktur'],
                            default          => [$role],
                        };

                        return $query->where(function (Builder $q) use ($matchRoles) {
                            $q->whereHas('roles', fn($sub) => $sub->whereIn('name', $matchRoles));
                            foreach ($matchRoles as $r) {
                                $q->orWhere('role', $r)
                                    ->orWhere('role', 'like', "%\"{$r}\"%")
                                    ->orWhere('role', 'like', "%,{$r},%")
                                    ->orWhere('role', 'like', "{$r},%")
                                    ->orWhere('role', 'like', "%,{$r}");
                            }
                        });
                    }),
            ])
            ->defaultSort('name', 'asc')
            ->searchPlaceholder('Cari nama atau username...')
            ->striped()
            ->paginated(false);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT DATA UNTUK RINGKASAN
    |--------------------------------------------------------------------------
    */
    protected function formatSummaryUser(User $user): array
    {
        $badges = $user->medsos_role_badges;
        $roleText = ! empty($badges)
            ? collect($badges)->map(fn($b) => "{$b['icon']} {$b['label']}")->join(', ')
            : ($user->roles?->pluck('name')->join(', ') ?: $user->role ?: '-');

        $words = preg_split('/\s+/', trim($user->name));
        $initials = count($words) >= 2
            ? strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1))
            : strtoupper(mb_substr($words[0] ?? '?', 0, 2));

        $avatarBg = match (true) {
            $user->isAdmin() => '#ef4444',
            $user->isMedsosPlanner() => '#f59e0b',
            $user->isMedsosEditor() => '#10b981',
            $user->isMedsosAdminPlatform() => '#3b82f6',
            $user->isInstruktur() => '#8b5cf6',
            default => '#64748b',
        };

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'initials' => $initials,
            'avatar_bg' => $avatarBg,
            'roles' => $roleText,
            'role_badges' => $badges,
            'bahan' => (int) ($user->bahan_count ?? 0),
            'final' => (int) ($user->final_count ?? 0),
            'editor' => (int) ($user->editor_finished_count ?? 0),
            'admin' => (int) ($user->admin_finished_count ?? 0),
            'active' => (int) ($user->active_content_count ?? 0),
            'editor_revision' => (int) ($user->editor_revision_count ?? 0),
            'total' => (int) ($user->total_kontribusi_summary ?? 0),
            'beban' => (int) ($user->beban_kerja_summary ?? 0),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RINGKASAN STATISTIK TIM UNTUK BLADE
    |--------------------------------------------------------------------------
    */
    public static function getTeamSummary(): array
    {
        $instance = new static();
        $weekStart = now()->startOfWeek()->startOfDay();
        $weekEnd = now()->endOfWeek()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | DATA SELURUH WAKTU DAN MINGGU INI
        |--------------------------------------------------------------------------
        */
        $allRows = $instance->statisticUserQuery()
            ->get()
            ->map(function (User $user) use ($instance) {
                $user->total_kontribusi_summary = $instance->calculateTotalContribution($user);

                return $user;
            });

        $weekRows = $instance->statisticUserQuery($weekStart, $weekEnd)
            ->get()
            ->map(function (User $user) use ($instance) {
                $user->total_kontribusi_summary = $instance->calculateTotalContribution($user);

                return $user;
            });

        /*
        |--------------------------------------------------------------------------
        | BEBAN KERJA AKTIF
        |--------------------------------------------------------------------------
        */
        $sharedEditorQueueCount = Content::query()
            ->where('status', 'menunggu_editor')
            ->count();

        $sharedAdminQueueCount = Content::query()
            ->where('status', 'siap_publish')
            ->count();

        $workloadRows = $allRows
            ->map(function (User $user) use ($instance, $sharedEditorQueueCount, $sharedAdminQueueCount) {
                $user->beban_kerja_summary = $instance->calculateWorkload(
                    $user,
                    $sharedEditorQueueCount,
                    $sharedAdminQueueCount
                );

                return $user;
            });

        /*
        |--------------------------------------------------------------------------
        | RANKING DAN DAFTAR
        |--------------------------------------------------------------------------
        */
        $topTotal = $allRows
            ->sortByDesc('total_kontribusi_summary')
            ->filter(fn(User $user) => (int) $user->total_kontribusi_summary > 0)
            ->take(5)
            ->values()
            ->map(fn(User $user) => $instance->formatSummaryUser($user))
            ->all();

        $topThisWeek = $weekRows
            ->sortByDesc('total_kontribusi_summary')
            ->filter(fn(User $user) => (int) $user->total_kontribusi_summary > 0)
            ->take(5)
            ->values()
            ->map(fn(User $user) => $instance->formatSummaryUser($user))
            ->all();

        $noContributionThisWeek = $weekRows
            ->filter(fn(User $user) => (int) $user->total_kontribusi_summary === 0)
            ->sortBy('name')
            ->values()
            ->map(fn(User $user) => $instance->formatSummaryUser($user))
            ->all();

        $highestWorkload = $workloadRows
            ->sortByDesc('beban_kerja_summary')
            ->filter(fn(User $user) => (int) ($user->beban_kerja_summary ?? 0) > 0)
            ->take(5)
            ->values()
            ->map(fn(User $user) => $instance->formatSummaryUser($user))
            ->all();

        return [
            'week_label' => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M Y'),

            'total_members' => $allRows->count(),
            'total_contribution_all' => $allRows->sum('total_kontribusi_summary'),
            'total_contribution_week' => $weekRows->sum('total_kontribusi_summary'),
            'total_active_workload' => $workloadRows->sum('beban_kerja_summary'),
            'total_active_contents' => Content::query()->where('status', '!=', 'selesai')->count(),
            'no_contribution_week_count' => count($noContributionThisWeek),

            'top_total' => $topTotal,
            'top_this_week' => $topThisWeek,
            'no_contribution_this_week' => $noContributionThisWeek,
            'highest_workload' => $highestWorkload,
        ];
    }
}
