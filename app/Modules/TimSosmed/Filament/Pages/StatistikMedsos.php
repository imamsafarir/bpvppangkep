<?php

namespace App\Modules\TimSosmed\Filament\Pages;

use App\Modules\TimSosmed\Models\SocialSetting;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StatistikMedsos extends Page
{
    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI HALAMAN FILAMENT
    |--------------------------------------------------------------------------
    */

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static string|\UnitEnum|null $navigationGroup = 'Tim Media Sosial';
    protected static ?string $navigationLabel = 'Statistik Medsos';
    protected static ?int $navigationSort = 3;

    protected string $view = 'timsosmed::filament.pages.statistik-medsos';
    protected ?string $heading = '📈 Insight & Statistik Media Sosial';
    protected ?string $subheading = 'Data performa akun Instagram Business berdasarkan Meta API.';

    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI META API DAN CACHE
    |--------------------------------------------------------------------------
    |
    | - Request umum dipotong maksimal 89 hari.
    | - Metric pertumbuhan dan aksi profil dipotong maksimal 28 hari karena
    |   metric tersebut lebih sensitif terhadap batas periode Meta.
    | - Hasil range yang sama disimpan sementara agar tidak selalu meminta API.
    |
    */

    private const META_GRAPH_VERSION = 'v25.0';
    private const META_HISTORY_YEARS = 2;
    private const META_GENERAL_CHUNK_DAYS = 89;
    private const META_GROWTH_CHUNK_DAYS = 28;
    private const ONLINE_FOLLOWERS_MAX_DAYS = 30;
    private const CACHE_TTL_MINUTES = 15;

    /*
    |--------------------------------------------------------------------------
    | STATUS HALAMAN DAN PROSES MANUAL
    |--------------------------------------------------------------------------
    |
    | Data tidak diambil otomatis ketika halaman dibuka atau filter berubah.
    | Pengguna harus menekan tombol "Ambil Data".
    |
    */

    public bool $hasLoadedData = false;
    public bool $filtersDirty = false;
    public ?string $lastLoadedAt = null;

    /*
    |--------------------------------------------------------------------------
    | DATA PROFIL, ERROR, WARNING, DAN DEBUG
    |--------------------------------------------------------------------------
    */

    public array $igData = [];
    public ?string $errorMessage = null;
    public ?string $compareErrorMessage = null;
    public array $metricWarnings = [];

    /**
     * Debug dinonaktifkan secara default agar payload Livewire tetap ringan.
     * Aktifkan sementara hanya ketika sedang memeriksa response Meta.
     */
    public bool $debugMode = false;
    public array $debugData = [];

    /*
    |--------------------------------------------------------------------------
    | DATA METRIC DAN STATUS
    |--------------------------------------------------------------------------
    |
    | Status metric:
    | pending     = belum dimuat
    | loaded      = tersedia dan nilainya lebih dari 0
    | empty       = tersedia tetapi nilainya 0
    | unavailable = tidak dikembalikan Meta
    | error       = request gagal
    | derived     = hasil perhitungan metric lain
    |
    */

    public array $metrics = [];
    public array $prevMetrics = [];
    public array $metricStatus = [];
    public array $prevMetricStatus = [];

    /*
    |--------------------------------------------------------------------------
    | DETAIL AKSI PROFIL DAN FOLLOWER ONLINE
    |--------------------------------------------------------------------------
    */

    public array $profileActionsBreakdown = [];
    public array $prevProfileActionsBreakdown = [];

    public array $onlineFollowersHourly = [];
    public array $prevOnlineFollowersHourly = [];
    public ?int $onlineFollowersPeakHour = null;
    public ?int $prevOnlineFollowersPeakHour = null;

    /*
    |--------------------------------------------------------------------------
    | DEMOGRAFI
    |--------------------------------------------------------------------------
    |
    | Demografi tetap dimuat manual melalui tombol terpisah.
    |
    */

    public array $demographics = [];
    public array $prevDemographics = [];
    public ?string $demographicStatusMessage = null;

    /*
    |--------------------------------------------------------------------------
    | PROPERTY LAMA UNTUK KOMPATIBILITAS
    |--------------------------------------------------------------------------
    */

    public int $views = 0;
    public int $reach = 0;
    public int $interactions = 0;
    public int $linkClicks = 0;
    public int $profileViews = 0;
    public int $followers = 0;

    public int $prevViews = 0;
    public int $prevReach = 0;
    public int $prevInteractions = 0;
    public int $prevLinkClicks = 0;
    public int $prevProfileViews = 0;
    public int $prevFollowers = 0;

    /*
    |--------------------------------------------------------------------------
    | FILTER PERIODE
    |--------------------------------------------------------------------------
    */

    public bool $isComparing = false;

    public string $selectedPeriod = 'yesterday';
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;

    public ?string $compareStartDate = null;
    public ?string $compareEndDate = null;
    public ?string $compareSelectedPeriod = null;

    public string $periodLabel = 'Data insight terakhir';
    public string $comparePeriodLabel = 'Data pembanding';
    public string $currentRangeLabel = '';
    public string $prevRangeLabel = '';

    /*
    |--------------------------------------------------------------------------
    | DAFTAR METRIC
    |--------------------------------------------------------------------------
    */

    protected array $metricKeys = [
        'views',
        'reach',
        'accounts_engaged',
        'total_interactions',
        'likes',
        'comments',
        'shares',
        'saves',
        'replies',
        'website_clicks',
        'profile_links_taps',
        'profile_actions',
        'profile_views',
        'online_followers',
        'follows',
        'unfollows',
        'net_follows',

        // Metric internal, tidak perlu ditampilkan sebagai kartu terpisah.
        'follower_count',
        'follows_and_unfollows',
    ];

    protected array $demographicMetricKeys = [
        'engaged_audience_demographics',
        'reached_audience_demographics',
        'follower_demographics',
    ];

    /*
    |--------------------------------------------------------------------------
    | DEFINISI METRIC UNTUK BLADE
    |--------------------------------------------------------------------------
    */

    public array $metricDefinitions = [
        'views' => [
            'label' => 'Views / Tayangan',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=views',
            'description' => 'Jumlah tayangan pada periode terpilih.',
        ],
        'reach' => [
            'label' => 'Reach / Jangkauan',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=reach',
            'description' => 'Jumlah akun unik yang berhasil dijangkau.',
        ],
        'accounts_engaged' => [
            'label' => 'Akun Terlibat',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=accounts_engaged',
            'description' => 'Jumlah akun unik yang berinteraksi dengan konten.',
        ],
        'total_interactions' => [
            'label' => 'Total Interaksi',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=total_interactions',
            'description' => 'Total interaksi konten yang dihitung Meta.',
        ],
        'likes' => [
            'label' => 'Likes',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=likes',
            'description' => 'Jumlah suka pada periode terpilih.',
        ],
        'comments' => [
            'label' => 'Komentar',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=comments',
            'description' => 'Jumlah komentar pada periode terpilih.',
        ],
        'shares' => [
            'label' => 'Shares',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=shares',
            'description' => 'Jumlah konten yang dibagikan.',
        ],
        'saves' => [
            'label' => 'Saves',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=saves',
            'description' => 'Jumlah konten yang disimpan.',
        ],
        'replies' => [
            'label' => 'Replies',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=replies',
            'description' => 'Jumlah balasan Story jika tersedia dari Meta.',
        ],
        'website_clicks' => [
            'label' => 'Klik Website / Bio',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=website_clicks',
            'description' => 'Jumlah klik tautan website atau tautan utama profil.',
        ],
        'profile_links_taps' => [
            'label' => 'Tap Tombol Profil',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=profile_links_taps, breakdown=contact_button_type',
            'description' => 'Tap pada tombol email, telepon, alamat, petunjuk arah, atau pesan.',
        ],
        'profile_actions' => [
            'label' => 'Total Aksi Profil',
            'group' => 'Instagram',
            'type' => 'derived',
            'source' => 'Gabungan profile_links_taps dan website_clicks',
            'description' => 'Total tindakan dari halaman profil tanpa menghitung website dua kali.',
        ],
        'profile_views' => [
            'label' => 'Kunjungan Profil',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=profile_views',
            'description' => 'Jumlah kunjungan ke profil Instagram.',
        ],
        'online_followers' => [
            'label' => 'Puncak Follower Online',
            'group' => 'Instagram',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=online_followers',
            'description' => 'Perkiraan follower online tertinggi berdasarkan distribusi jam.',
        ],
        'follows' => [
            'label' => 'Follow Baru',
            'group' => 'Instagram Growth',
            'type' => 'number',
            'source' => 'Instagram User Insights: follows_and_unfollows atau follower_count',
            'description' => 'Jumlah akun yang mulai mengikuti Instagram.',
        ],
        'unfollows' => [
            'label' => 'Unfollow',
            'group' => 'Instagram Growth',
            'type' => 'number',
            'source' => 'Instagram User Insights: metric=follows_and_unfollows',
            'description' => 'Jumlah akun yang berhenti mengikuti Instagram.',
        ],
        'net_follows' => [
            'label' => 'Pertumbuhan Bersih',
            'group' => 'Instagram Growth',
            'type' => 'derived',
            'source' => 'Follow Baru - Unfollow',
            'description' => 'Pertumbuhan bersih pengikut.',
        ],
        'follower_count' => [
            'label' => 'Follower Count Internal',
            'group' => 'Internal',
            'type' => 'internal',
            'source' => 'Instagram User Insights: metric=follower_count',
            'description' => 'Fallback internal untuk Follow Baru.',
        ],
        'follows_and_unfollows' => [
            'label' => 'Follow dan Unfollow Mentah',
            'group' => 'Internal',
            'type' => 'internal',
            'source' => 'Instagram User Insights: metric=follows_and_unfollows',
            'description' => 'Metric internal untuk proses pertumbuhan pengikut.',
        ],
        'engaged_audience_demographics' => [
            'label' => 'Demografi Audiens Terlibat',
            'group' => 'Demografi',
            'type' => 'demographic',
            'source' => 'Instagram User Insights: metric=engaged_audience_demographics',
            'description' => 'Demografi akun yang berinteraksi dengan konten.',
        ],
        'reached_audience_demographics' => [
            'label' => 'Demografi Audiens Terjangkau',
            'group' => 'Demografi',
            'type' => 'demographic',
            'source' => 'Instagram User Insights: metric=reached_audience_demographics',
            'description' => 'Demografi akun yang berhasil dijangkau.',
        ],
        'follower_demographics' => [
            'label' => 'Demografi Follower',
            'group' => 'Demografi',
            'type' => 'demographic',
            'source' => 'Instagram User Insights: metric=follower_demographics',
            'description' => 'Demografi follower akun Instagram.',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | MOUNT: HANYA MENYIAPKAN FILTER, TANPA REQUEST META
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->resetAllMetrics();
        $this->applyQuickRange('yesterday');
        $this->filtersDirty = false;
    }

    /*
    |--------------------------------------------------------------------------
    | HEADER ACTIONS
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [
            Action::make('load_instagram_data')
                ->label(fn(): string => $this->hasLoadedData ? 'Ambil Ulang Data' : 'Ambil Data')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('primary')
                ->action(fn() => $this->loadInstagramData()),

            Action::make('force_refresh_instagram')
                ->label('Refresh Paksa')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Cache untuk periode aktif akan dilewati dan data diminta ulang dari Meta API.')
                ->action(fn() => $this->loadInstagramData(force: true))
                ->visible(fn(): bool => $this->hasLoadedData),

            Action::make('login_facebook')
                ->label('Hubungkan ke Facebook Business')
                ->icon('heroicon-o-link')
                ->color('primary')
                ->url(route('facebook.login'))
                ->hidden(
                    fn(): bool => SocialSetting::query()
                        ->where('provider_name', 'facebook')
                        ->whereNotNull('access_token')
                        ->exists()
                ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PERUBAHAN FILTER: TIDAK MENJALANKAN META API
    |--------------------------------------------------------------------------
    |
    | Hook ini hanya menandai bahwa filter berubah. Pastikan input tanggal di
    | Blade memakai wire:model, bukan wire:model.live.
    |
    */

    public function updatedCustomStartDate(): void
    {
        $this->selectedPeriod = 'custom';
        $this->markFiltersDirty();
    }

    public function updatedCustomEndDate(): void
    {
        $this->selectedPeriod = 'custom';
        $this->markFiltersDirty();
    }

    public function updatedCompareStartDate(): void
    {
        $this->compareSelectedPeriod = 'custom';
        $this->markFiltersDirty();
    }

    public function updatedCompareEndDate(): void
    {
        $this->compareSelectedPeriod = 'custom';
        $this->markFiltersDirty();
    }

    protected function markFiltersDirty(): void
    {
        $this->filtersDirty = true;
        $this->compareErrorMessage = null;
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER PERIODE CEPAT
    |--------------------------------------------------------------------------
    |
    | Method ini hanya mengisi tanggal. Tidak mengambil data Meta.
    |
    */

    public function applyQuickRange(string $period, string $target = 'current'): void
    {
        [$start, $end, $label] = $this->buildDateRange($period);

        if ($target === 'compare') {
            $this->compareSelectedPeriod = $period;
            $this->compareStartDate = $start->toDateString();
            $this->compareEndDate = $end->toDateString();
            $this->comparePeriodLabel = str_replace('Data', 'Pembanding', $label);
        } else {
            $this->selectedPeriod = $period;
            $this->customStartDate = $start->toDateString();
            $this->customEndDate = $end->toDateString();
            $this->periodLabel = $label;
        }

        $this->markFiltersDirty();
    }

    public function toggleComparison(): void
    {
        $this->isComparing = ! $this->isComparing;

        if ($this->isComparing) {
            if (! $this->compareStartDate || ! $this->compareEndDate) {
                $this->fillDefaultComparisonRange();
            }
        } else {
            $this->resetComparisonData();
            $this->compareStartDate = null;
            $this->compareEndDate = null;
            $this->compareSelectedPeriod = null;
            $this->comparePeriodLabel = 'Data pembanding';
        }

        $this->markFiltersDirty();
    }

    /*
    |--------------------------------------------------------------------------
    | RANGE TANGGAL
    |--------------------------------------------------------------------------
    |
    | Insight harian terbaru dianggap selesai pada H-2.
    | Contoh 14 Juli 2026: tanggal insight terakhir adalah 12 Juli 2026.
    |
    */

    protected function buildDateRange(string $period): array
    {
        $now = now();
        $latestInsightDate = $now->copy()->subDays(2);
        $latestStart = $latestInsightDate->copy()->startOfDay();
        $latestEnd = $latestInsightDate->copy()->endOfDay();

        $thisWeekStart = $now->copy()->startOfWeek()->startOfDay();
        $thisMonthStart = $now->copy()->startOfMonth()->startOfDay();
        $thisYearStart = $now->copy()->startOfYear()->startOfDay();

        if ($thisWeekStart->greaterThan($latestEnd)) {
            $thisWeekStart = $latestStart->copy();
        }

        if ($thisMonthStart->greaterThan($latestEnd)) {
            $thisMonthStart = $latestStart->copy();
        }

        if ($thisYearStart->greaterThan($latestEnd)) {
            $thisYearStart = $latestStart->copy();
        }

        return match ($period) {
            'yesterday' => [
                $latestStart,
                $latestEnd,
                'Data insight terakhir',
            ],
            '7_days' => [
                $latestEnd->copy()->subDays(6)->startOfDay(),
                $latestEnd->copy(),
                'Data 7 hari terakhir',
            ],
            '28_days' => [
                $latestEnd->copy()->subDays(27)->startOfDay(),
                $latestEnd->copy(),
                'Data 28 hari terakhir',
            ],
            '90_days' => [
                $latestEnd->copy()->subDays(89)->startOfDay(),
                $latestEnd->copy(),
                'Data 90 hari terakhir',
            ],
            'this_week' => [
                $thisWeekStart,
                $latestEnd->copy(),
                'Data minggu ini',
            ],
            'this_month' => [
                $thisMonthStart,
                $latestEnd->copy(),
                'Data bulan ini',
            ],
            'this_year' => [
                $thisYearStart,
                $latestEnd->copy(),
                'Data tahun ini',
            ],
            'last_week' => [
                $now->copy()->subWeek()->startOfWeek()->startOfDay(),
                $now->copy()->subWeek()->endOfWeek()->endOfDay(),
                'Data minggu lalu',
            ],
            'last_month' => [
                $now->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay(),
                $now->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay(),
                'Data bulan lalu',
            ],
            default => [
                $latestStart,
                $latestEnd,
                'Data insight terakhir',
            ],
        };
    }

    protected function fillDefaultComparisonRange(): void
    {
        if (! $this->customStartDate || ! $this->customEndDate) {
            [$start, $end] = $this->buildDateRange($this->selectedPeriod);
            $this->customStartDate = $start->toDateString();
            $this->customEndDate = $end->toDateString();
        }

        $start = Carbon::createFromFormat('Y-m-d', $this->customStartDate, 'UTC')->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $this->customEndDate, 'UTC')->endOfDay();

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $days = max(1, $start->diffInDays($end) + 1);
        $compareEnd = $start->copy()->subDay()->endOfDay();
        $compareStart = $compareEnd->copy()->subDays($days - 1)->startOfDay();

        $this->compareStartDate = $compareStart->toDateString();
        $this->compareEndDate = $compareEnd->toDateString();
        $this->compareSelectedPeriod = null;
        $this->comparePeriodLabel = 'Pembanding periode sebelumnya';
    }

    protected function latestInsightDateString(): string
    {
        return now()->subDays(2)->toDateString();
    }

    protected function normalizeDateSelection(
        ?string $startDate,
        ?string $endDate,
        bool $updateCurrentProperties = false,
        bool $updateCompareProperties = false
    ): array {
        if (! $startDate || ! $endDate) {
            throw new \InvalidArgumentException('Tanggal awal dan tanggal akhir wajib dipilih.');
        }

        $start = Carbon::createFromFormat('Y-m-d', $startDate, 'UTC')->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate, 'UTC')->endOfDay();
        $latestAllowed = Carbon::createFromFormat('Y-m-d', $this->latestInsightDateString(), 'UTC')->endOfDay();

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        if ($end->greaterThan($latestAllowed)) {
            $end = $latestAllowed->copy();
        }

        if ($start->greaterThan($end)) {
            $start = $end->copy()->startOfDay();
        }

        if ($updateCurrentProperties) {
            $this->customStartDate = $start->toDateString();
            $this->customEndDate = $end->toDateString();
        }

        if ($updateCompareProperties) {
            $this->compareStartDate = $start->toDateString();
            $this->compareEndDate = $end->toDateString();
        }

        return [$start->timestamp, $end->timestamp];
    }

    protected function getCurrentTimestamps(): array
    {
        if (! $this->customStartDate || ! $this->customEndDate) {
            [$start, $end, $label] = $this->buildDateRange($this->selectedPeriod);
            $this->customStartDate = $start->toDateString();
            $this->customEndDate = $end->toDateString();
            $this->periodLabel = $label;
        }

        if ($this->selectedPeriod === 'custom') {
            $this->periodLabel = 'Data ' . Carbon::parse($this->customStartDate)->format('d/m/Y') .
                ' - ' . Carbon::parse($this->customEndDate)->format('d/m/Y');
        }

        return $this->normalizeDateSelection(
            $this->customStartDate,
            $this->customEndDate,
            updateCurrentProperties: true
        );
    }

    protected function getCompareTimestamps(): array
    {
        return $this->normalizeDateSelection(
            $this->compareStartDate,
            $this->compareEndDate,
            updateCompareProperties: true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NILAI DEFAULT DAN RESET
    |--------------------------------------------------------------------------
    */

    protected function emptyMetricValues(): array
    {
        return array_fill_keys($this->metricKeys, 0);
    }

    protected function emptyMetricStatuses(string $status = 'pending'): array
    {
        return array_fill_keys($this->metricKeys, $status);
    }

    protected function resetAllMetrics(): void
    {
        $this->metrics = $this->emptyMetricValues();
        $this->prevMetrics = $this->emptyMetricValues();
        $this->metricStatus = $this->emptyMetricStatuses();
        $this->prevMetricStatus = $this->emptyMetricStatuses();

        $this->profileActionsBreakdown = [];
        $this->prevProfileActionsBreakdown = [];
        $this->onlineFollowersHourly = [];
        $this->prevOnlineFollowersHourly = [];
        $this->onlineFollowersPeakHour = null;
        $this->prevOnlineFollowersPeakHour = null;
        $this->demographics = [];
        $this->prevDemographics = [];

        $this->syncLegacyProperties();
        $this->syncLegacyPrevProperties();
    }

    protected function resetMainData(): void
    {
        $this->metrics = $this->emptyMetricValues();
        $this->metricStatus = $this->emptyMetricStatuses();
        $this->metricWarnings = [];
        $this->profileActionsBreakdown = [];
        $this->onlineFollowersHourly = [];
        $this->onlineFollowersPeakHour = null;
        $this->demographics = [];
        $this->demographicStatusMessage = null;
        $this->syncLegacyProperties();
    }

    protected function resetComparisonData(): void
    {
        $this->prevMetrics = $this->emptyMetricValues();
        $this->prevMetricStatus = $this->emptyMetricStatuses();
        $this->prevProfileActionsBreakdown = [];
        $this->prevOnlineFollowersHourly = [];
        $this->prevOnlineFollowersPeakHour = null;
        $this->prevDemographics = [];
        $this->prevRangeLabel = '';
        $this->compareErrorMessage = null;
        $this->syncLegacyPrevProperties();
    }

    protected function syncLegacyProperties(): void
    {
        $this->views = (int) ($this->metrics['views'] ?? 0);
        $this->reach = (int) ($this->metrics['reach'] ?? 0);
        $this->interactions = (int) ($this->metrics['total_interactions'] ?? 0);
        $this->linkClicks = (int) ($this->metrics['profile_actions'] ?? 0);
        $this->profileViews = (int) ($this->metrics['profile_views'] ?? 0);
        $this->followers = (int) ($this->metrics['follows'] ?? 0);
    }

    protected function syncLegacyPrevProperties(): void
    {
        $this->prevViews = (int) ($this->prevMetrics['views'] ?? 0);
        $this->prevReach = (int) ($this->prevMetrics['reach'] ?? 0);
        $this->prevInteractions = (int) ($this->prevMetrics['total_interactions'] ?? 0);
        $this->prevLinkClicks = (int) ($this->prevMetrics['profile_actions'] ?? 0);
        $this->prevProfileViews = (int) ($this->prevMetrics['profile_views'] ?? 0);
        $this->prevFollowers = (int) ($this->prevMetrics['follows'] ?? 0);
    }

    /*
    |--------------------------------------------------------------------------
    | DEBUG DAN HTTP
    |--------------------------------------------------------------------------
    */

    protected function resetDebugData(): void
    {
        $this->debugData = [];
    }

    protected function addDebug(string $title, array $data = []): void
    {
        if (! $this->debugMode || count($this->debugData) >= 80) {
            return;
        }

        $this->debugData[] = [
            'time' => now()->format('H:i:s'),
            'title' => $title,
            'data' => $this->sanitizeDebugData($data),
        ];
    }

    protected function sanitizeDebugData(mixed $value): mixed
    {
        if (is_array($value)) {
            $clean = [];

            foreach ($value as $key => $item) {
                $lowerKey = strtolower((string) $key);

                if (
                    str_contains($lowerKey, 'token') ||
                    str_contains($lowerKey, 'authorization')
                ) {
                    $clean[$key] = '[DISEMBUNYIKAN]';
                    continue;
                }

                $clean[$key] = $this->sanitizeDebugData($item);
            }

            return $clean;
        }

        if (is_string($value)) {
            $value = preg_replace('/access_token=([^&\s]+)/', 'access_token=[DISEMBUNYIKAN]', $value);

            return strlen($value) > 2000
                ? substr($value, 0, 2000) . '... [DIPOTONG]'
                : $value;
        }

        return $value;
    }

    protected function graphUrl(string $path): string
    {
        return sprintf(
            'https://graph.facebook.com/%s/%s',
            self::META_GRAPH_VERSION,
            ltrim($path, '/')
        );
    }

    protected function metaGet(string $url, array $params): Response
    {
        $params = array_filter($params, fn(mixed $value): bool => $value !== null);
        $startedAt = microtime(true);

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->connectTimeout(7)
                ->withOptions([CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4])
                ->get($url, $params);

            $this->addDebug('Meta API Request', [
                'url' => $url,
                'params' => $params,
                'status' => $response->status(),
                'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
                'error' => $response->json('error.message'),
                'data' => $response->json('data'),
            ]);

            return $response;
        } catch (\Throwable $exception) {
            $this->addDebug('Meta API Exception', [
                'url' => $url,
                'params' => $params,
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER STATUS DAN PENGGABUNGAN HASIL
    |--------------------------------------------------------------------------
    */

    protected function metricStatusPriority(string $status): int
    {
        return match ($status) {
            'loaded', 'derived' => 5,
            'empty' => 4,
            'unavailable' => 3,
            'error' => 2,
            'pending' => 1,
            default => 0,
        };
    }

    protected function mergeMetricStatus(string $current, string $incoming): string
    {
        return $this->metricStatusPriority($incoming) > $this->metricStatusPriority($current)
            ? $incoming
            : $current;
    }

    protected function mergeMetricResult(
        array &$metrics,
        array &$statuses,
        array &$warnings,
        string $metric,
        int $value,
        string $status,
        ?string $warning = null,
        bool $sum = true
    ): void {
        if (! array_key_exists($metric, $metrics)) {
            return;
        }

        $metrics[$metric] = $sum ? $metrics[$metric] + $value : $value;
        $statuses[$metric] = $this->mergeMetricStatus($statuses[$metric] ?? 'pending', $status);

        if ($warning) {
            $existing = $warnings[$metric] ?? null;
            $warnings[$metric] = $existing && $existing !== $warning
                ? $existing . ' | ' . $warning
                : $warning;
        }
    }

    protected function mergeBreakdownValues(array &$target, array $incoming): void
    {
        foreach ($incoming as $label => $value) {
            $target[$label] = ($target[$label] ?? 0) + (int) $value;
        }

        arsort($target);
    }

    /*
    |--------------------------------------------------------------------------
    | PARSER NUMERIC UMUM
    |--------------------------------------------------------------------------
    */

    protected function extractMetricNumericValue(array $metricData): int
    {
        $directValue = data_get($metricData, 'total_value.value');

        if (is_numeric($directValue)) {
            return (int) $directValue;
        }

        $breakdownTotal = 0;
        $hasBreakdown = false;

        foreach (data_get($metricData, 'total_value.breakdowns', []) as $breakdown) {
            foreach (($breakdown['results'] ?? []) as $row) {
                if (is_numeric($row['value'] ?? null)) {
                    $breakdownTotal += (int) $row['value'];
                    $hasBreakdown = true;
                }
            }
        }

        if ($hasBreakdown) {
            return $breakdownTotal;
        }

        $valuesTotal = 0;
        $hasValues = false;

        foreach (($metricData['values'] ?? []) as $valueData) {
            if (is_numeric($valueData['value'] ?? null)) {
                $valuesTotal += (int) $valueData['value'];
                $hasValues = true;
            }
        }

        return $hasValues ? $valuesTotal : 0;
    }

    protected function parseNumericInsightResponse(array $data, array $metrics): array
    {
        $values = array_fill_keys($metrics, 0);
        $seen = array_fill_keys($metrics, false);

        foreach ($data as $metricData) {
            if (! is_array($metricData)) {
                continue;
            }

            $name = $metricData['name'] ?? null;

            if (! $name || ! array_key_exists($name, $values)) {
                continue;
            }

            $seen[$name] = true;
            $values[$name] += $this->extractMetricNumericValue($metricData);
        }

        return ['values' => $values, 'seen' => $seen];
    }

    protected function metricResultFromParsed(array $parsed, string $metric): array
    {
        $seen = (bool) ($parsed['seen'][$metric] ?? false);
        $value = (int) ($parsed['values'][$metric] ?? 0);

        if (! $seen) {
            return [
                'value' => 0,
                'status' => 'unavailable',
                'warning' => "Meta tidak mengembalikan metric {$metric} untuk periode ini.",
            ];
        }

        return [
            'value' => $value,
            'status' => $value > 0 ? 'loaded' : 'empty',
            'warning' => null,
        ];
    }

    protected function fetchSingleNumericMetric(
        string $igId,
        string $token,
        string $metric,
        int $since,
        int $until,
        array $extraParams = []
    ): array {
        $response = $this->metaGet(
            $this->graphUrl("{$igId}/insights"),
            array_merge([
                'metric' => $metric,
                'period' => 'day',
                'metric_type' => 'total_value',
                'since' => $since,
                'until' => $until,
                'access_token' => $token,
            ], $extraParams)
        );

        if (! $response->successful()) {
            return [
                'value' => 0,
                'status' => 'error',
                'warning' => $response->json('error.message') ?? "Metric {$metric} tidak tersedia.",
            ];
        }

        return $this->metricResultFromParsed(
            $this->parseNumericInsightResponse($response->json('data', []), [$metric]),
            $metric
        );
    }

    protected function fetchInsightGroupWithFallback(
        string $igId,
        string $token,
        array $metrics,
        int $since,
        int $until,
        array $extraParams = []
    ): array {
        $values = array_fill_keys($metrics, 0);
        $statuses = array_fill_keys($metrics, 'pending');
        $warnings = [];

        $response = $this->metaGet(
            $this->graphUrl("{$igId}/insights"),
            array_merge([
                'metric' => implode(',', $metrics),
                'period' => 'day',
                'metric_type' => 'total_value',
                'since' => $since,
                'until' => $until,
                'access_token' => $token,
            ], $extraParams)
        );

        $parsed = $response->successful()
            ? $this->parseNumericInsightResponse($response->json('data', []), $metrics)
            : ['values' => $values, 'seen' => array_fill_keys($metrics, false)];

        foreach ($metrics as $metric) {
            if ($response->successful() && ($parsed['seen'][$metric] ?? false)) {
                $result = $this->metricResultFromParsed($parsed, $metric);
            } else {
                // Fallback hanya untuk metric yang hilang atau ketika request kelompok gagal.
                $result = $this->fetchSingleNumericMetric(
                    $igId,
                    $token,
                    $metric,
                    $since,
                    $until,
                    $extraParams
                );
            }

            $values[$metric] = (int) ($result['value'] ?? 0);
            $statuses[$metric] = $result['status'] ?? 'unavailable';

            if ($result['warning'] ?? null) {
                $warnings[$metric] = $result['warning'];
            }
        }

        return compact('values', 'statuses', 'warnings');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEWS / TAYANGAN KHUSUS
    |--------------------------------------------------------------------------
    |
    | Meta menyediakan total biasa dan breakdown. Ketiganya tidak dijumlahkan;
    | sistem memilih kandidat terbesar yang valid agar sesuai dengan Insight.
    |
    */

    protected function fetchViewsMetric(
        string $igId,
        string $token,
        int $since,
        int $until
    ): array {
        $requests = [
            'total' => [],
            'media_product_type' => ['breakdown' => 'media_product_type'],
            'follower_type' => ['breakdown' => 'follower_type'],
        ];

        $candidates = [];
        $errors = [];

        foreach ($requests as $source => $extraParams) {
            $response = $this->metaGet(
                $this->graphUrl("{$igId}/insights"),
                array_merge([
                    'metric' => 'views',
                    'period' => 'day',
                    'metric_type' => 'total_value',
                    'since' => $since,
                    'until' => $until,
                    'access_token' => $token,
                ], $extraParams)
            );

            if (! $response->successful()) {
                $errors[] = $response->json('error.message') ?? "Request views {$source} gagal.";
                continue;
            }

            $data = $response->json('data', []);
            $metricSeen = false;
            $directValue = null;
            $breakdownTotal = 0;
            $hasBreakdown = false;
            $valuesTotal = 0;
            $hasValues = false;

            foreach ($data as $metricData) {
                if (($metricData['name'] ?? null) !== 'views') {
                    continue;
                }

                $metricSeen = true;
                $rawDirect = data_get($metricData, 'total_value.value');

                if (is_numeric($rawDirect)) {
                    $directValue = (int) $rawDirect;
                }

                foreach (data_get($metricData, 'total_value.breakdowns', []) as $breakdown) {
                    foreach (($breakdown['results'] ?? []) as $row) {
                        if (is_numeric($row['value'] ?? null)) {
                            $breakdownTotal += (int) $row['value'];
                            $hasBreakdown = true;
                        }
                    }
                }

                foreach (($metricData['values'] ?? []) as $valueData) {
                    if (is_numeric($valueData['value'] ?? null)) {
                        $valuesTotal += (int) $valueData['value'];
                        $hasValues = true;
                    }
                }
            }

            if (! $metricSeen) {
                continue;
            }

            $candidates[$source] = $hasBreakdown
                ? $breakdownTotal
                : ($directValue ?? ($hasValues ? $valuesTotal : 0));
        }

        if ($candidates !== []) {
            $value = max($candidates);

            $this->addDebug('Kandidat Views', [
                'candidates' => $candidates,
                'selected' => array_search($value, $candidates, true),
            ]);

            return [
                'value' => $value,
                'status' => $value > 0 ? 'loaded' : 'empty',
                'warning' => null,
            ];
        }

        return [
            'value' => 0,
            'status' => $errors !== [] ? 'error' : 'unavailable',
            'warning' => collect($errors)->filter()->unique()->join(' | ')
                ?: 'Meta tidak mengembalikan metric views.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | AKSI TOMBOL PROFIL
    |--------------------------------------------------------------------------
    |
    | Request utama memakai breakdown contact_button_type. Request tanpa
    | breakdown hanya dijalankan jika request utama gagal atau metric hilang.
    |
    */

    protected function parseProfileLinksBreakdown(array $data): array
    {
        $result = [];

        foreach ($data as $metricData) {
            if (($metricData['name'] ?? null) !== 'profile_links_taps') {
                continue;
            }

            foreach (data_get($metricData, 'total_value.breakdowns', []) as $breakdown) {
                foreach (($breakdown['results'] ?? []) as $row) {
                    $label = collect($row['dimension_values'] ?? [])
                        ->map(fn(mixed $value): string => strtoupper(trim((string) $value)))
                        ->filter()
                        ->join(' / ');

                    $label = $label !== '' ? $label : 'LAINNYA';
                    $result[$label] = ($result[$label] ?? 0) + (int) ($row['value'] ?? 0);
                }
            }
        }

        arsort($result);

        return $result;
    }

    protected function fetchProfileLinksTapsMetric(
        string $igId,
        string $token,
        int $since,
        int $until
    ): array {
        $endpoint = $this->graphUrl("{$igId}/insights");
        $base = [
            'metric' => 'profile_links_taps',
            'period' => 'day',
            'metric_type' => 'total_value',
            'since' => $since,
            'until' => $until,
            'access_token' => $token,
        ];

        $primary = $this->metaGet($endpoint, array_merge($base, [
            'breakdown' => 'contact_button_type',
        ]));

        if ($primary->successful()) {
            $data = $primary->json('data', []);
            $parsed = $this->parseNumericInsightResponse($data, ['profile_links_taps']);

            if ($parsed['seen']['profile_links_taps'] ?? false) {
                $result = $this->metricResultFromParsed($parsed, 'profile_links_taps');
                $result['breakdown'] = $this->parseProfileLinksBreakdown($data);

                return $result;
            }
        }

        $fallback = $this->metaGet($endpoint, $base);

        if ($fallback->successful()) {
            $parsed = $this->parseNumericInsightResponse(
                $fallback->json('data', []),
                ['profile_links_taps']
            );
            $result = $this->metricResultFromParsed($parsed, 'profile_links_taps');
            $result['breakdown'] = [];

            return $result;
        }

        return [
            'value' => 0,
            'status' => 'error',
            'warning' => $fallback->json('error.message')
                ?? $primary->json('error.message')
                ?? 'Metric profile_links_taps tidak tersedia.',
            'breakdown' => [],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FOLLOW DAN UNFOLLOW
    |--------------------------------------------------------------------------
    |
    | Alur ringan:
    | 1. Coba breakdown follow_type.
    | 2. Jika breakdown tersedia, tidak meminta total/follower_count lagi.
    | 3. Fallback total dan follower_count hanya ketika breakdown tidak tersedia.
    |
    */

    protected function fetchFollowsAndUnfollows(
        string $igId,
        string $token,
        int $since,
        int $until
    ): array {
        $endpoint = $this->graphUrl("{$igId}/insights");
        $base = [
            'metric' => 'follows_and_unfollows',
            'period' => 'day',
            'metric_type' => 'total_value',
            'since' => $since,
            'until' => $until,
            'access_token' => $token,
        ];

        $breakdownResponse = $this->metaGet($endpoint, array_merge($base, [
            'breakdown' => 'follow_type',
        ]));

        $follows = 0;
        $unfollows = 0;
        $hasRecognizedBreakdown = false;

        if ($breakdownResponse->successful()) {
            foreach ($breakdownResponse->json('data', []) as $metricData) {
                if (($metricData['name'] ?? null) !== 'follows_and_unfollows') {
                    continue;
                }

                foreach (data_get($metricData, 'total_value.breakdowns', []) as $breakdown) {
                    foreach (($breakdown['results'] ?? []) as $row) {
                        $value = (int) ($row['value'] ?? 0);
                        $dimensions = collect($row['dimension_values'] ?? [])
                            ->map(fn(mixed $item): string => strtoupper(trim((string) $item)))
                            ->all();

                        foreach ($dimensions as $dimension) {
                            if (in_array($dimension, ['FOLLOW', 'FOLLOWS', 'FOLLOWED', 'FOLLOWER'], true)) {
                                $follows += $value;
                                $hasRecognizedBreakdown = true;
                                break;
                            }

                            if (in_array($dimension, ['UNFOLLOW', 'UNFOLLOWS', 'UNFOLLOWED', 'NON_FOLLOWER'], true)) {
                                $unfollows += $value;
                                $hasRecognizedBreakdown = true;
                                break;
                            }
                        }
                    }
                }
            }
        }

        if ($hasRecognizedBreakdown) {
            $total = $follows + $unfollows;

            return [
                'total' => $total,
                'follows' => $follows,
                'unfollows' => $unfollows,
                'net_follows' => $follows - $unfollows,
                'follower_count' => $follows,
                'status' => $total > 0 ? 'loaded' : 'empty',
                'warning' => null,
            ];
        }

        // Fallback hanya ketika breakdown tidak tersedia.
        $totalResult = $this->fetchSingleNumericMetric(
            $igId,
            $token,
            'follows_and_unfollows',
            $since,
            $until
        );

        $followerCountResponse = $this->metaGet($endpoint, [
            'metric' => 'follower_count',
            'period' => 'day',
            'since' => $since,
            'until' => $until,
            'access_token' => $token,
        ]);

        $followerCountResult = $followerCountResponse->successful()
            ? $this->metricResultFromParsed(
                $this->parseNumericInsightResponse(
                    $followerCountResponse->json('data', []),
                    ['follower_count']
                ),
                'follower_count'
            )
            : [
                'value' => 0,
                'status' => 'error',
                'warning' => $followerCountResponse->json('error.message'),
            ];

        $total = (int) ($totalResult['value'] ?? 0);
        $follows = (int) ($followerCountResult['value'] ?? 0);
        $unfollows = max($total - $follows, 0);

        if ($total === 0 && $follows > 0) {
            $total = $follows;
        }

        $hasSuccessfulFallback = in_array($totalResult['status'] ?? '', ['loaded', 'empty'], true)
            || in_array($followerCountResult['status'] ?? '', ['loaded', 'empty'], true);

        return [
            'total' => $total,
            'follows' => $follows,
            'unfollows' => $unfollows,
            'net_follows' => $follows - $unfollows,
            'follower_count' => $follows,
            'status' => ($total > 0 || $follows > 0)
                ? 'loaded'
                : ($hasSuccessfulFallback ? 'empty' : 'unavailable'),
            'warning' => collect([
                $breakdownResponse->json('error.message'),
                $totalResult['warning'] ?? null,
                $followerCountResult['warning'] ?? null,
            ])->filter()->unique()->join(' | ') ?: null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ONLINE FOLLOWERS
    |--------------------------------------------------------------------------
    */

    protected function parseOnlineFollowersData(array $data): array
    {
        $hourSamples = [];
        $scalarValues = [];

        $collect = function (mixed $value) use (&$hourSamples, &$scalarValues): void {
            if (is_numeric($value)) {
                $scalarValues[] = (int) $value;
                return;
            }

            if (! is_array($value)) {
                return;
            }

            foreach ($value as $hour => $count) {
                if (! is_numeric($hour) || ! is_numeric($count)) {
                    continue;
                }

                $hour = (int) $hour;

                if ($hour >= 0 && $hour <= 23) {
                    $hourSamples[$hour] ??= [];
                    $hourSamples[$hour][] = (int) $count;
                }
            }
        };

        foreach ($data as $metricData) {
            if (($metricData['name'] ?? null) !== 'online_followers') {
                continue;
            }

            $collect(data_get($metricData, 'total_value.value'));

            foreach (($metricData['values'] ?? []) as $valueData) {
                $collect($valueData['value'] ?? null);
            }
        }

        $hourly = [];

        foreach ($hourSamples as $hour => $samples) {
            if ($samples !== []) {
                $hourly[$hour] = (int) round(array_sum($samples) / count($samples));
            }
        }

        if ($hourly !== []) {
            $sorted = $hourly;
            arsort($sorted);
            $peakHour = array_key_first($sorted);
            ksort($hourly);

            return [
                'seen' => true,
                'value' => (int) ($sorted[$peakHour] ?? 0),
                'peak_hour' => $peakHour,
                'hourly' => $hourly,
            ];
        }

        if ($scalarValues !== []) {
            return [
                'seen' => true,
                'value' => max($scalarValues),
                'peak_hour' => null,
                'hourly' => [],
            ];
        }

        return ['seen' => false, 'value' => 0, 'peak_hour' => null, 'hourly' => []];
    }

    protected function fetchOnlineFollowersMetric(
        string $igId,
        string $token,
        int $since,
        int $until
    ): array {
        $minimumSince = Carbon::now('UTC')
            ->subDays(self::ONLINE_FOLLOWERS_MAX_DAYS - 1)
            ->startOfDay()
            ->timestamp;

        $limitedSince = max($since, $minimumSince);
        $limitedUntil = min($until, Carbon::now('UTC')->timestamp);

        if ($limitedSince >= $limitedUntil) {
            return [
                'value' => 0,
                'status' => 'unavailable',
                'warning' => 'Online followers hanya tersedia untuk data terbaru sekitar 30 hari.',
                'peak_hour' => null,
                'hourly' => [],
            ];
        }

        $errors = [];

        foreach (['lifetime', 'day'] as $period) {
            $response = $this->metaGet($this->graphUrl("{$igId}/insights"), [
                'metric' => 'online_followers',
                'period' => $period,
                'since' => $limitedSince,
                'until' => $limitedUntil,
                'access_token' => $token,
            ]);

            if (! $response->successful()) {
                $errors[] = $response->json('error.message');
                continue;
            }

            $parsed = $this->parseOnlineFollowersData($response->json('data', []));

            if ($parsed['seen']) {
                return [
                    'value' => (int) $parsed['value'],
                    'status' => $parsed['value'] > 0 ? 'loaded' : 'empty',
                    'warning' => null,
                    'peak_hour' => $parsed['peak_hour'],
                    'hourly' => $parsed['hourly'],
                ];
            }
        }

        return [
            'value' => 0,
            'status' => $errors !== [] ? 'error' : 'unavailable',
            'warning' => collect($errors)->filter()->unique()->join(' | ')
                ?: 'Meta tidak mengembalikan distribusi online followers.',
            'peak_hour' => null,
            'hourly' => [],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CHUNK RANGE
    |--------------------------------------------------------------------------
    */

    protected function buildTimestampChunks(int $since, int $until, int $maxDays): array
    {
        $chunks = [];
        $cursor = Carbon::createFromTimestamp($since, 'UTC');
        $rangeEnd = Carbon::createFromTimestamp($until, 'UTC');

        while ($cursor->lessThanOrEqualTo($rangeEnd)) {
            $chunkEnd = $cursor->copy()->addDays($maxDays - 1)->endOfDay();

            if ($chunkEnd->greaterThan($rangeEnd)) {
                $chunkEnd = $rangeEnd->copy();
            }

            $chunks[] = [$cursor->timestamp, $chunkEnd->timestamp];
            $cursor = $chunkEnd->copy()->addSecond();
        }

        return $chunks;
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL AKSI PROFIL
    |--------------------------------------------------------------------------
    */

    protected function breakdownContainsWebsite(array $breakdown): bool
    {
        foreach (array_keys($breakdown) as $label) {
            $label = strtoupper((string) $label);

            if (
                str_contains($label, 'WEBSITE') ||
                str_contains($label, 'WEB') ||
                str_contains($label, 'LINK')
            ) {
                return true;
            }
        }

        return false;
    }

    protected function calculateProfileActions(
        int $profileLinksTaps,
        int $websiteClicks,
        array $breakdown
    ): int {
        return $this->breakdownContainsWebsite($breakdown)
            ? max($profileLinksTaps, $websiteClicks)
            : $profileLinksTaps + $websiteClicks;
    }

    protected function determineProfileActionsStatus(
        int $value,
        string $profileLinksStatus,
        string $websiteStatus
    ): string {
        if ($value > 0) {
            return 'derived';
        }

        if (
            in_array($profileLinksStatus, ['loaded', 'empty'], true) ||
            in_array($websiteStatus, ['loaded', 'empty'], true)
        ) {
            return 'empty';
        }

        return $profileLinksStatus === 'error' && $websiteStatus === 'error'
            ? 'error'
            : 'unavailable';
    }

    /*
    |--------------------------------------------------------------------------
    | PENGAMBILAN SELURUH INSIGHT UNTUK SATU RANGE
    |--------------------------------------------------------------------------
    |
    | Request dibagi menjadi dua lintasan:
    | - Metric umum: chunk 89 hari.
    | - Pertumbuhan dan aksi profil: chunk 28 hari.
    |
    */

    private function getMetaInsightsData(
        string $igId,
        string $token,
        int $since,
        int $until
    ): array {
        $metrics = $this->emptyMetricValues();
        $statuses = $this->emptyMetricStatuses('unavailable');
        $warnings = [];
        $profileActionsBreakdown = [];

        // Metric umum: lebih sedikit chunk agar lebih ringan.
        foreach ($this->buildTimestampChunks($since, $until, self::META_GENERAL_CHUNK_DAYS) as [$chunkSince, $chunkUntil]) {
            $mainMetrics = [
                'reach',
                'total_interactions',
                'accounts_engaged',
                'website_clicks',
                'profile_views',
            ];

            $mainResult = $this->fetchInsightGroupWithFallback(
                $igId,
                $token,
                $mainMetrics,
                $chunkSince,
                $chunkUntil
            );

            foreach ($mainMetrics as $metric) {
                $this->mergeMetricResult(
                    $metrics,
                    $statuses,
                    $warnings,
                    $metric,
                    (int) ($mainResult['values'][$metric] ?? 0),
                    $mainResult['statuses'][$metric] ?? 'unavailable',
                    $mainResult['warnings'][$metric] ?? null
                );
            }

            $engagementMetrics = ['likes', 'comments', 'shares', 'saves'];
            $engagementResult = $this->fetchInsightGroupWithFallback(
                $igId,
                $token,
                $engagementMetrics,
                $chunkSince,
                $chunkUntil,
                ['breakdown' => 'media_product_type']
            );

            foreach ($engagementMetrics as $metric) {
                $this->mergeMetricResult(
                    $metrics,
                    $statuses,
                    $warnings,
                    $metric,
                    (int) ($engagementResult['values'][$metric] ?? 0),
                    $engagementResult['statuses'][$metric] ?? 'unavailable',
                    $engagementResult['warnings'][$metric] ?? null
                );
            }

            $viewsResult = $this->fetchViewsMetric(
                $igId,
                $token,
                $chunkSince,
                $chunkUntil
            );

            $this->mergeMetricResult(
                $metrics,
                $statuses,
                $warnings,
                'views',
                (int) ($viewsResult['value'] ?? 0),
                $viewsResult['status'] ?? 'unavailable',
                $viewsResult['warning'] ?? null
            );

            $repliesResult = $this->fetchSingleNumericMetric(
                $igId,
                $token,
                'replies',
                $chunkSince,
                $chunkUntil
            );

            $this->mergeMetricResult(
                $metrics,
                $statuses,
                $warnings,
                'replies',
                (int) ($repliesResult['value'] ?? 0),
                $repliesResult['status'] ?? 'unavailable',
                $repliesResult['warning'] ?? null
            );
        }

        // Metric pertumbuhan dan aksi profil: chunk lebih pendek.
        foreach ($this->buildTimestampChunks($since, $until, self::META_GROWTH_CHUNK_DAYS) as [$chunkSince, $chunkUntil]) {
            $profileLinksResult = $this->fetchProfileLinksTapsMetric(
                $igId,
                $token,
                $chunkSince,
                $chunkUntil
            );

            $this->mergeMetricResult(
                $metrics,
                $statuses,
                $warnings,
                'profile_links_taps',
                (int) ($profileLinksResult['value'] ?? 0),
                $profileLinksResult['status'] ?? 'unavailable',
                $profileLinksResult['warning'] ?? null
            );

            $this->mergeBreakdownValues(
                $profileActionsBreakdown,
                $profileLinksResult['breakdown'] ?? []
            );

            $growthResult = $this->fetchFollowsAndUnfollows(
                $igId,
                $token,
                $chunkSince,
                $chunkUntil
            );

            $growthStatus = $growthResult['status'] ?? 'unavailable';
            $growthWarning = $growthResult['warning'] ?? null;

            foreach (
                [
                    'follows' => 'follows',
                    'unfollows' => 'unfollows',
                    'follower_count' => 'follower_count',
                    'follows_and_unfollows' => 'total',
                ] as $metric => $resultKey
            ) {
                $this->mergeMetricResult(
                    $metrics,
                    $statuses,
                    $warnings,
                    $metric,
                    (int) ($growthResult[$resultKey] ?? 0),
                    $growthStatus,
                    $metric === 'follows' || $metric === 'unfollows' ? $growthWarning : null
                );
            }

            $this->mergeMetricResult(
                $metrics,
                $statuses,
                $warnings,
                'net_follows',
                (int) ($growthResult['net_follows'] ?? 0),
                $growthStatus === 'loaded' ? 'derived' : $growthStatus,
                $growthWarning
            );
        }

        // Online followers bukan metric kumulatif, sehingga hanya diminta sekali.
        $onlineResult = $this->fetchOnlineFollowersMetric($igId, $token, $since, $until);

        $this->mergeMetricResult(
            $metrics,
            $statuses,
            $warnings,
            'online_followers',
            (int) ($onlineResult['value'] ?? 0),
            $onlineResult['status'] ?? 'unavailable',
            $onlineResult['warning'] ?? null,
            sum: false
        );

        // Metric turunan Total Aksi Profil.
        $websiteClicks = (int) ($metrics['website_clicks'] ?? 0);
        $profileLinksTaps = (int) ($metrics['profile_links_taps'] ?? 0);

        if ($websiteClicks > 0 && ! $this->breakdownContainsWebsite($profileActionsBreakdown)) {
            $profileActionsBreakdown['WEBSITE / BIO'] = $websiteClicks;
        }

        $profileActions = $this->calculateProfileActions(
            $profileLinksTaps,
            $websiteClicks,
            $profileActionsBreakdown
        );

        $metrics['profile_actions'] = $profileActions;
        $statuses['profile_actions'] = $this->determineProfileActionsStatus(
            $profileActions,
            $statuses['profile_links_taps'] ?? 'unavailable',
            $statuses['website_clicks'] ?? 'unavailable'
        );

        arsort($profileActionsBreakdown);

        return [
            'metrics' => $metrics,
            'statuses' => $statuses,
            'warnings' => $warnings,
            'profile_actions_breakdown' => $profileActionsBreakdown,
            'online_followers_hourly' => $onlineResult['hourly'] ?? [],
            'online_followers_peak_hour' => $onlineResult['peak_hour'] ?? null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CACHE
    |--------------------------------------------------------------------------
    */

    protected function profileCacheKey(string $igId): string
    {
        return "statistik-medsos:profile:" . self::META_GRAPH_VERSION . ":{$igId}";
    }

    protected function insightsCacheKey(string $igId, int $since, int $until): string
    {
        return "statistik-medsos:insights:v4:" . self::META_GRAPH_VERSION . ":{$igId}:{$since}:{$until}";
    }

    protected function getCachedProfile(string $igId, string $token, bool $force): array
    {
        $key = $this->profileCacheKey($igId);

        if (! $force && is_array($cached = Cache::get($key))) {
            return $cached;
        }

        $response = $this->metaGet($this->graphUrl($igId), [
            'fields' => 'id,username,followers_count,media_count,profile_picture_url',
            'access_token' => $token,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                $response->json('error.message') ?? 'Gagal mengambil profil Instagram.'
            );
        }

        $profile = $response->json();
        Cache::put($key, $profile, now()->addMinutes(self::CACHE_TTL_MINUTES));

        return $profile;
    }

    protected function getCachedInsights(
        string $igId,
        string $token,
        int $since,
        int $until,
        bool $force
    ): array {
        $key = $this->insightsCacheKey($igId, $since, $until);

        if (! $force && is_array($cached = Cache::get($key))) {
            return $cached;
        }

        $data = $this->getMetaInsightsData($igId, $token, $since, $until);

        $hasUsableMetric = collect($data['statuses'] ?? [])->contains(
            fn(string $status): bool => in_array($status, ['loaded', 'empty', 'derived'], true)
        );

        if ($hasUsableMetric) {
            Cache::put($key, $data, now()->addMinutes(self::CACHE_TTL_MINUTES));
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA MANUAL
    |--------------------------------------------------------------------------
    */

    public function loadInstagramData(bool $force = false): void
    {
        $this->resetDebugData();
        $this->errorMessage = null;
        $this->compareErrorMessage = null;

        $socialSetting = SocialSetting::query()
            ->where('provider_name', 'facebook')
            ->first();

        $token = $socialSetting?->access_token;
        $configuredIgId = $socialSetting?->ig_user_id;

        if (! $token || ! $configuredIgId) {
            $this->errorMessage = 'Akun belum terhubung ke Meta. Hubungkan akun Facebook Business terlebih dahulu.';
            $this->hasLoadedData = false;
            return;
        }

        try {
            [$since, $until] = $this->getCurrentTimestamps();
            [$since, $until] = $this->normalizeMetaRange($since, $until);

            $this->resetMainData();

            $this->igData = $this->getCachedProfile((string) $configuredIgId, $token, $force);
            $realIgId = (string) ($this->igData['id'] ?? $configuredIgId);

            $currentData = $this->getCachedInsights($realIgId, $token, $since, $until, $force);
            $this->assignCurrentData($currentData, $since, $until);

            if ($this->isComparing) {
                $this->loadComparisonData($realIgId, $token, $force);
            } else {
                $this->resetComparisonData();
            }

            $this->hasLoadedData = true;
            $this->filtersDirty = false;
            $this->lastLoadedAt = now()->format('d M Y H:i:s');
        } catch (\Throwable $exception) {
            $message = $exception->getMessage();
            $lower = strtolower($message);

            $this->errorMessage = (
                str_contains($lower, 'expired') ||
                str_contains($lower, 'invalid token')
            )
                ? 'Sesi Facebook telah berakhir. Hubungkan ulang akun Meta.'
                : 'Error Sistem: ' . $message;

            logger()->error('Statistik Medsos Error', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }
    }

    /**
     * Alias untuk kompatibilitas dengan Blade atau action lama.
     */
    public function fetchInstagramData(): void
    {
        $this->loadInstagramData();
    }

    protected function assignCurrentData(array $data, int $since, int $until): void
    {
        $this->metrics = array_replace($this->emptyMetricValues(), $data['metrics'] ?? []);
        $this->metricStatus = array_replace($this->emptyMetricStatuses(), $data['statuses'] ?? []);
        $this->metricWarnings = $data['warnings'] ?? [];
        $this->profileActionsBreakdown = $data['profile_actions_breakdown'] ?? [];
        $this->onlineFollowersHourly = $data['online_followers_hourly'] ?? [];
        $this->onlineFollowersPeakHour = $data['online_followers_peak_hour'] ?? null;
        $this->currentRangeLabel = $this->formatRangeLabel($since, $until);
        $this->syncLegacyProperties();
    }

    protected function loadComparisonData(
        string $igId,
        string $token,
        bool $force = false
    ): void {
        try {
            [$since, $until] = $this->getCompareTimestamps();
            [$since, $until] = $this->normalizeMetaRange($since, $until);

            $data = $this->getCachedInsights($igId, $token, $since, $until, $force);

            $this->prevMetrics = array_replace($this->emptyMetricValues(), $data['metrics'] ?? []);
            $this->prevMetricStatus = array_replace($this->emptyMetricStatuses(), $data['statuses'] ?? []);
            $this->prevProfileActionsBreakdown = $data['profile_actions_breakdown'] ?? [];
            $this->prevOnlineFollowersHourly = $data['online_followers_hourly'] ?? [];
            $this->prevOnlineFollowersPeakHour = $data['online_followers_peak_hour'] ?? null;
            $this->prevRangeLabel = $this->formatRangeLabel($since, $until);
            $this->comparePeriodLabel = 'Data ' .
                Carbon::createFromTimestamp($since, 'UTC')->format('d/m/Y') . ' - ' .
                Carbon::createFromTimestamp($until, 'UTC')->format('d/m/Y');

            $this->syncLegacyPrevProperties();
        } catch (\Throwable $exception) {
            $this->resetComparisonData();
            $this->compareErrorMessage = 'Gagal memuat data pembanding: ' . $exception->getMessage();
        }
    }

    protected function formatRangeLabel(int $since, int $until): string
    {
        return Carbon::createFromTimestamp($since, 'UTC')->format('d M') .
            ' - ' .
            Carbon::createFromTimestamp($until, 'UTC')->format('d M, Y');
    }

    /*
    |--------------------------------------------------------------------------
    | DEMOGRAFI MANUAL DAN CACHE
    |--------------------------------------------------------------------------
    */

    public function loadDemographicMetric(string $metric): void
    {
        if (! in_array($metric, $this->demographicMetricKeys, true)) {
            $this->demographicStatusMessage = 'Metric demografi tidak valid.';
            return;
        }

        if (! $this->hasLoadedData || empty($this->igData)) {
            $this->demographicStatusMessage = 'Ambil data utama terlebih dahulu.';
            return;
        }

        $socialSetting = SocialSetting::query()
            ->where('provider_name', 'facebook')
            ->first();

        $token = $socialSetting?->access_token;
        $igId = (string) ($this->igData['id'] ?? $socialSetting?->ig_user_id);

        if (! $token || ! $igId) {
            $this->demographicStatusMessage = 'Akun Meta belum terhubung.';
            return;
        }

        try {
            $timeframe = in_array($this->selectedPeriod, [
                'yesterday',
                '7_days',
                'this_week',
                'last_week',
            ], true) ? 'this_week' : 'this_month';

            $cacheKey = "statistik-medsos:demographic:" . self::META_GRAPH_VERSION . ":{$igId}:{$metric}:{$timeframe}";

            $this->demographics[$metric] = Cache::remember(
                $cacheKey,
                now()->addMinutes(self::CACHE_TTL_MINUTES),
                fn(): array => $this->fetchInstagramDemographics($igId, $token, $metric, $timeframe)
            );

            $this->demographicStatusMessage = empty($this->demographics[$metric])
                ? 'Meta tidak mengembalikan data demografi untuk metric tersebut.'
                : 'Data demografi berhasil dimuat.';
        } catch (\Throwable $exception) {
            $this->demographicStatusMessage = 'Gagal memuat demografi: ' . $exception->getMessage();
        }
    }

    protected function fetchInstagramDemographics(
        string $igId,
        string $token,
        string $metric,
        string $timeframe
    ): array {
        $result = [];

        foreach (['age', 'gender', 'country', 'city'] as $breakdown) {
            $response = $this->metaGet($this->graphUrl("{$igId}/insights"), [
                'metric' => $metric,
                'period' => 'lifetime',
                'metric_type' => 'total_value',
                'breakdown' => $breakdown,
                'timeframe' => $timeframe,
                'access_token' => $token,
            ]);

            if (! $response->successful()) {
                $this->metricWarnings["{$metric}_{$breakdown}"] =
                    $response->json('error.message')
                    ?? "Demografi {$metric} {$breakdown} tidak tersedia.";
                continue;
            }

            $rows = data_get(
                $response->json(),
                'data.0.total_value.breakdowns.0.results',
                []
            );

            $result[$breakdown] = collect($rows)
                ->map(fn(array $row): array => [
                    'label' => collect($row['dimension_values'] ?? [])->join(' / '),
                    'value' => (int) ($row['value'] ?? 0),
                ])
                ->filter(fn(array $row): bool => filled($row['label']) && $row['value'] > 0)
                ->sortByDesc('value')
                ->values()
                ->take(10)
                ->all();
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALISASI RANGE META
    |--------------------------------------------------------------------------
    */

    protected function normalizeMetaRange(int $since, int $until): array
    {
        $minimumSince = Carbon::now('UTC')
            ->subYears(self::META_HISTORY_YEARS)
            ->addDay()
            ->startOfDay()
            ->timestamp;

        $maximumUntil = Carbon::createFromFormat(
            'Y-m-d',
            $this->latestInsightDateString(),
            'UTC'
        )->endOfDay()->timestamp;

        $since = max($since, $minimumSince);
        $until = min($until, $maximumUntil);

        if ($since >= $until) {
            $fallbackDate = $this->latestInsightDateString();
            $since = Carbon::createFromFormat('Y-m-d', $fallbackDate, 'UTC')->startOfDay()->timestamp;
            $until = Carbon::createFromFormat('Y-m-d', $fallbackDate, 'UTC')->endOfDay()->timestamp;
        }

        return [$since, $until];
    }
}
