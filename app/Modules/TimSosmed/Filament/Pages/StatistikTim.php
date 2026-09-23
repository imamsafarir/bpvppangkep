<?php

namespace App\Modules\TimSosmed\Filament\Pages;

use App\Modules\TimSosmed\Models\Content;
use App\Models\User;
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

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static string|\UnitEnum|null $navigationGroup = 'Tim Media Sosial';
    protected static ?string $navigationLabel = 'Statistik Tim';
    protected static ?int $navigationSort = 2;

    protected string $view = 'timsosmed::filament.pages.statistik-tim';

    protected ?string $heading = '📊 Statistik Performa Tim';
    protected ?string $subheading = 'Pantau kontribusi kerja real-time dan beban tugas aktif setiap anggota tim.';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Modules\TimSosmed\Filament\Widgets\BebanKerjaOverview::class,
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
    | QUERY USER TIM
    |--------------------------------------------------------------------------
    | Untuk sementara semua user dibaca dulu agar statistik tidak 0.
    | Kalau nanti mau mengecualikan akun dummy, kita tambahkan lagi setelah data
    | sudah terbukti terbaca.
    */
    protected function teamUserQuery(): Builder
    {
        return User::query();
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
        $roleSortSubQuery = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->selectRaw('MIN(roles.name)')
            ->whereColumn('model_has_roles.model_id', 'users.id')
            ->where('model_has_roles.model_type', User::class);

        $editorRoleCountSubQuery = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->selectRaw('COUNT(*)')
            ->whereColumn('model_has_roles.model_id', 'users.id')
            ->where('model_has_roles.model_type', User::class)
            ->whereIn('roles.name', ['super_admin', 'editor']);

        $adminRoleCountSubQuery = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->selectRaw('COUNT(*)')
            ->whereColumn('model_has_roles.model_id', 'users.id')
            ->where('model_has_roles.model_type', User::class)
            ->whereIn('roles.name', ['super_admin', 'admin_platform']);

        return $this->teamUserQuery()
            ->with('roles')
            ->select('users.*')

            ->selectSub($roleSortSubQuery, 'role_sort')
            ->selectSub($editorRoleCountSubQuery, 'editor_role_count')
            ->selectSub($adminRoleCountSubQuery, 'admin_role_count')

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

        $beban += (int) ($user->active_content_count ?? 0);

        if ($user->hasRole(['super_admin', 'editor'])) {
            $beban += $sharedEditorQueueCount;
            $beban += (int) ($user->editor_revision_count ?? 0);
        }

        if ($user->hasRole(['super_admin', 'admin_platform'])) {
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
            ->query(
                $this->statisticUserQuery($start, $end)
            )
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->state(static function ($rowLoop): int {
                        return $rowLoop->iteration;
                    })
                    ->alignCenter(),

                TextColumn::make('name')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('primary')
                    ->separator(',')
                    ->sortable(
                        query: fn(Builder $query, string $direction): Builder =>
                        $query->orderBy('role_sort', $direction)
                    ),

                TextColumn::make('kontribusi_peran')
                    ->label('Kontribusi')
                    ->html()
                    ->sortable(
                        query: fn(Builder $query, string $direction): Builder =>
                        $query->orderByRaw("{$totalContributionSortSql} {$direction}")
                    )
                    ->state(function (User $record) {
                        $bahan = (int) ($record->bahan_count ?? 0);
                        $final = (int) ($record->final_count ?? 0);
                        $editor = (int) ($record->editor_finished_count ?? 0);
                        $admin = (int) ($record->admin_finished_count ?? 0);

                        $html = "
                            <div class='text-xs space-y-1 p-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10 w-fit min-w-[140px]'>
                                <div>
                                    <span class='text-gray-500 font-medium'>Konten Bahan:</span>
                                    <span class='font-bold text-warning-600'>{$bahan}</span>
                                </div>

                                <div>
                                    <span class='text-gray-500 font-medium'>Konten Final:</span>
                                    <span class='font-bold text-info-600'>{$final}</span>
                                </div>
                        ";

                        if ($record->hasRole(['super_admin', 'editor'])) {
                            $html .= "
                                <div>
                                    <span class='text-gray-500 font-medium'>Selesai Edit:</span>
                                    <span class='font-bold text-danger-600'>{$editor}</span>
                                </div>
                            ";
                        }

                        if ($record->hasRole(['super_admin', 'admin_platform'])) {
                            $html .= "
                                <div>
                                    <span class='text-gray-500 font-medium'>Tayang:</span>
                                    <span class='font-bold text-success-600'>{$admin}</span>
                                </div>
                            ";
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    }),

                TextColumn::make('beban_kerja')
                    ->label('Beban Kerja')
                    ->sortable(
                        query: fn(Builder $query, string $direction): Builder =>
                        $query->orderByRaw("{$workloadSortSql} {$direction}")
                    )
                    ->state(
                        fn(User $record) => $this->calculateWorkload(
                            $record,
                            $sharedEditorQueueCount,
                            $sharedAdminQueueCount
                        )
                    )
                    ->badge()
                    ->color(fn($state) => ((int) $state) === 0 ? 'success' : 'danger')
                    ->icon(fn($state) => ((int) $state) === 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle'),

                TextColumn::make('total_kontribusi')
                    ->label('Total Kontribusi')
                    ->sortable(
                        query: fn(Builder $query, string $direction): Builder =>
                        $query->orderByRaw("{$totalContributionSortSql} {$direction}")
                    )
                    ->state(
                        fn(User $record) => $this->calculateTotalContribution($record)
                    )
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('periode_pengerjaan')
                    ->label('Periode Pengerjaan')
                    ->placeholder('Semua Data')
                    ->options([
                        'this_month' => 'Bulan Ini',
                        'last_month' => 'Bulan Lalu',
                        'this_year' => 'Tahun Ini',
                    ])
                    ->query(fn($query) => $query),

                SelectFilter::make('role')
                    ->label('Filter Role')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
            ])
            ->defaultSort('name', 'asc')
            ->searchPlaceholder('Cari nama...')
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
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'roles' => $user->roles?->pluck('name')->join(', ') ?: '-',
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
    public function getTeamSummary(): array
    {
        $weekStart = now()->startOfWeek()->startOfDay();
        $weekEnd = now()->endOfWeek()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | DATA SELURUH WAKTU DAN MINGGU INI
        |--------------------------------------------------------------------------
        */
        $allRows = $this->statisticUserQuery()
            ->get()
            ->map(function (User $user) {
                $user->total_kontribusi_summary = $this->calculateTotalContribution($user);

                return $user;
            });

        $weekRows = $this->statisticUserQuery($weekStart, $weekEnd)
            ->get()
            ->map(function (User $user) {
                $user->total_kontribusi_summary = $this->calculateTotalContribution($user);

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
            ->map(function (User $user) use ($sharedEditorQueueCount, $sharedAdminQueueCount) {
                $user->beban_kerja_summary = $this->calculateWorkload(
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
            ->map(fn(User $user) => $this->formatSummaryUser($user))
            ->all();

        $topThisWeek = $weekRows
            ->sortByDesc('total_kontribusi_summary')
            ->filter(fn(User $user) => (int) $user->total_kontribusi_summary > 0)
            ->take(5)
            ->values()
            ->map(fn(User $user) => $this->formatSummaryUser($user))
            ->all();

        $noContributionThisWeek = $weekRows
            ->filter(fn(User $user) => (int) $user->total_kontribusi_summary === 0)
            ->sortBy('name')
            ->values()
            ->map(fn(User $user) => $this->formatSummaryUser($user))
            ->all();

        $highestWorkload = $workloadRows
            ->sortByDesc('beban_kerja_summary')
            ->filter(fn(User $user) => (int) ($user->beban_kerja_summary ?? 0) > 0)
            ->take(5)
            ->values()
            ->map(fn(User $user) => $this->formatSummaryUser($user))
            ->all();

        /*
        |--------------------------------------------------------------------------
        | DEBUG RINGKAS
        |--------------------------------------------------------------------------
        | Ini berguna untuk memastikan query sudah membaca user dan content.
        */
        $debug = [
            'total_users_db' => User::query()->count(),
            'total_contents_db' => Content::query()->count(),
            'users_terbaca_statistik' => $allRows->count(),
            'contents_minggu_ini' => Content::query()
                ->whereBetween('updated_at', [$weekStart, $weekEnd])
                ->count(),
        ];

        return [
            'week_label' => $weekStart->format('d M Y') . ' - ' . $weekEnd->format('d M Y'),

            'total_members' => $allRows->count(),
            'total_contribution_all' => $allRows->sum('total_kontribusi_summary'),
            'total_contribution_week' => $weekRows->sum('total_kontribusi_summary'),
            'no_contribution_week_count' => count($noContributionThisWeek),

            'top_total' => $topTotal,
            'top_this_week' => $topThisWeek,
            'no_contribution_this_week' => $noContributionThisWeek,
            'highest_workload' => $highestWorkload,

            'debug' => $debug,
        ];
    }
}
