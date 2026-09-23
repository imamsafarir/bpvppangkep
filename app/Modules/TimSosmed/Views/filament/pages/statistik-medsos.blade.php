<x-filament-panels::page>
    {{--
    |--------------------------------------------------------------------------
    | TAILWIND KHUSUS HALAMAN
    |--------------------------------------------------------------------------
    |
    | Hapus CDN ini apabila seluruh class halaman sudah masuk ke build Tailwind
    | aplikasi. Preflight dimatikan agar tidak bertabrakan dengan Filament.
    |
    --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            corePlugins: {
                preflight: false,
            },
        }
    </script>

    <style>
        .stat-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, .7) transparent;
        }

        .stat-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .stat-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .stat-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, .7);
            border-radius: 9999px;
        }

        .dark .stat-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, .85);
        }

        .stat-loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(255, 255, 255, .72);
            backdrop-filter: blur(4px);
        }

        .dark .stat-loading-overlay {
            background: rgba(3, 7, 18, .78);
        }

        .stat-card {
            border: 1px solid rgb(229 231 235);
            border-radius: 1rem;
            background: rgb(255 255 255);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        }

        .dark .stat-card {
            border-color: rgb(31 41 55);
            background: rgb(17 24 39);
        }

        .stat-soft-grid {
            background-image:
                linear-gradient(rgba(148, 163, 184, .08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, .08) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .stat-progress {
            overflow: hidden;
            height: .65rem;
            border-radius: 9999px;
            background: rgb(229 231 235);
        }

        .dark .stat-progress {
            background: rgb(55 65 81);
        }

        .stat-progress > span {
            display: block;
            height: 100%;
            border-radius: inherit;
            transition: width .45s ease;
        }
    </style>

    @php
        /*
        |--------------------------------------------------------------------------
        | DATA DASAR
        |--------------------------------------------------------------------------
        */
        $metrics = $this->metrics ?? [];
        $prevMetrics = $this->prevMetrics ?? [];
        $metricStatus = $this->metricStatus ?? [];
        $prevMetricStatus = $this->prevMetricStatus ?? [];
        $metricDefinitions = $this->metricDefinitions ?? [];
        $metricWarnings = $this->metricWarnings ?? [];
        $profileActionsBreakdown = $this->profileActionsBreakdown ?? [];
        $onlineFollowersHourly = $this->onlineFollowersHourly ?? [];
        $demographics = $this->demographics ?? [];

        /*
        |--------------------------------------------------------------------------
        | HELPER ANGKA DAN STATUS
        |--------------------------------------------------------------------------
        */
        $formatNumber = fn($value) => number_format((int) ($value ?? 0), 0, ',', '.');
        $formatDecimal = fn($value, int $decimal = 1) => number_format((float) ($value ?? 0), $decimal, ',', '.');

        $getMetric = fn(string $key): int => (int) data_get($metrics, $key, 0);
        $getPrevMetric = fn(string $key): int => (int) data_get($prevMetrics, $key, 0);
        $getMetricStatus = fn(string $key): string => (string) data_get($metricStatus, $key, 'pending');
        $getPrevMetricStatus = fn(string $key): string => (string) data_get($prevMetricStatus, $key, 'pending');

        $isAvailableStatus = fn(?string $status): bool => in_array(
            $status,
            ['loaded', 'empty', 'derived'],
            true,
        );

        $statusLabel = fn(?string $status): string => match ($status) {
            'loaded' => 'Tersedia',
            'empty' => 'Tidak ada aktivitas',
            'derived' => 'Hasil perhitungan',
            'unavailable' => 'Tidak tersedia',
            'error' => 'Gagal diambil',
            default => 'Belum dimuat',
        };

        $statusClasses = fn(?string $status): string => match ($status) {
            'loaded' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/20 dark:text-emerald-300',
            'empty' => 'bg-gray-100 text-gray-600 ring-gray-500/20 dark:bg-gray-800 dark:text-gray-300',
            'derived' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/20 dark:text-blue-300',
            'error' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/20 dark:text-red-300',
            'unavailable' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/20 dark:text-amber-300',
            default => 'bg-gray-100 text-gray-500 ring-gray-500/20 dark:bg-gray-800 dark:text-gray-400',
        };

        $displayMetricValue = function (string $metricKey, int $value, string $status) use (
            $formatNumber,
            $isAvailableStatus,
        ): string {
            if (!$isAvailableStatus($status)) {
                return 'Tidak tersedia';
            }

            $prefix = $metricKey === 'net_follows' && $value > 0 ? '+' : '';

            return $prefix . $formatNumber($value);
        };

        $safeRate = function (int|float $numerator, int|float $denominator, int $decimals = 1): float {
            if ($denominator <= 0) {
                return 0;
            }

            return round(($numerator / $denominator) * 100, $decimals);
        };

        $renderTrend = function (
            int $current,
            int $previous,
            string $currentStatus,
            string $previousStatus,
            bool $isComparing,
        ) use ($formatNumber, $isAvailableStatus): string {
            if (!$isComparing) {
                return '';
            }

            if (!$isAvailableStatus($currentStatus) || !$isAvailableStatus($previousStatus)) {
                return '<div class="mt-3 text-xs font-medium text-gray-400">Perbandingan tidak tersedia</div>';
            }

            $difference = $current - $previous;
            $percentage = $previous > 0
                ? round(($difference / $previous) * 100, 1)
                : ($current > 0 ? 100 : 0);

            $positive = $difference >= 0;
            $class = $positive
                ? 'text-emerald-600 dark:text-emerald-400'
                : 'text-red-600 dark:text-red-400';
            $symbol = $positive ? '↑' : '↓';

            return '<div class="mt-3 text-sm font-semibold ' . $class . '">' .
                $symbol . ' ' . abs($percentage) . '% ' .
                '<span class="ml-1 text-xs font-normal text-gray-500 dark:text-gray-400">vs ' .
                $formatNumber($previous) . '</span></div>';
        };

        /*
        |--------------------------------------------------------------------------
        | FILTER CEPAT
        |--------------------------------------------------------------------------
        */
        $quickRanges = [
            'yesterday' => 'Kemarin',
            '7_days' => '7 Hari',
            '28_days' => '28 Hari',
            '90_days' => '90 Hari',
            'this_week' => 'Minggu Ini',
            'this_month' => 'Bulan Ini',
            'this_year' => 'Tahun Ini',
            'last_week' => 'Minggu Lalu',
            'last_month' => 'Bulan Lalu',
        ];

        /*
        |--------------------------------------------------------------------------
        | KONFIGURASI TAMPILAN METRIC
        |--------------------------------------------------------------------------
        */
        $metricUi = [
            'views' => ['icon' => 'heroicon-o-eye', 'color' => 'info'],
            'reach' => ['icon' => 'heroicon-o-chart-bar', 'color' => 'success'],
            'accounts_engaged' => ['icon' => 'heroicon-o-sparkles', 'color' => 'success'],
            'total_interactions' => ['icon' => 'heroicon-o-heart', 'color' => 'danger'],
            'likes' => ['icon' => 'heroicon-o-hand-thumb-up', 'color' => 'danger'],
            'comments' => ['icon' => 'heroicon-o-chat-bubble-left-right', 'color' => 'info'],
            'shares' => ['icon' => 'heroicon-o-share', 'color' => 'warning'],
            'saves' => ['icon' => 'heroicon-o-bookmark', 'color' => 'primary'],
            'replies' => ['icon' => 'heroicon-o-arrow-uturn-left', 'color' => 'gray'],
            'website_clicks' => ['icon' => 'heroicon-o-globe-alt', 'color' => 'warning'],
            'profile_links_taps' => ['icon' => 'heroicon-o-cursor-arrow-rays', 'color' => 'warning'],
            'profile_actions' => ['icon' => 'heroicon-o-cursor-arrow-ripple', 'color' => 'primary'],
            'profile_views' => ['icon' => 'heroicon-o-user-circle', 'color' => 'primary'],
            'online_followers' => ['icon' => 'heroicon-o-clock', 'color' => 'info'],
            'follows' => ['icon' => 'heroicon-o-user-plus', 'color' => 'success'],
            'unfollows' => ['icon' => 'heroicon-o-user-minus', 'color' => 'danger'],
            'net_follows' => ['icon' => 'heroicon-o-arrow-trending-up', 'color' => 'primary'],
        ];

        $mainCards = [
            'views',
            'reach',
            'total_interactions',
            'profile_views',
            'profile_actions',
            'follows',
            'unfollows',
            'net_follows',
        ];

        $detailMetricGroups = [
            'Awareness dan Jangkauan' => ['views', 'reach', 'accounts_engaged'],
            'Interaksi Konten' => ['total_interactions', 'likes', 'comments', 'shares', 'saves', 'replies'],
            'Aktivitas Profil' => ['profile_views', 'online_followers'],
        ];

        $demographicMetrics = [
            'follower_demographics',
            'reached_audience_demographics',
            'engaged_audience_demographics',
        ];

        $comparisonMetricKeys = collect($metricDefinitions)
            ->filter(function ($definition, $key) {
                return in_array($definition['type'] ?? '', ['number', 'derived'], true)
                    && !in_array($key, ['follower_count', 'follows_and_unfollows'], true);
            })
            ->keys()
            ->values()
            ->all();

        $comparisonChartMetrics = [
            'views',
            'reach',
            'total_interactions',
            'profile_views',
            'profile_actions',
            'follows',
            'net_follows',
        ];

        $hasComparisonResult =
            $isComparing
            && filled($prevRangeLabel)
            && !$compareErrorMessage
            && !$filtersDirty;

        /*
        |--------------------------------------------------------------------------
        | ANALISIS RASIO
        |--------------------------------------------------------------------------
        |
        | Semua rasio dihitung dari data yang sudah dimuat. Tidak ada request API
        | tambahan untuk menampilkan bagian analisis ini.
        |
        */
        $viewsValue = max(0, $getMetric('views'));
        $reachValue = max(0, $getMetric('reach'));
        $engagedValue = max(0, $getMetric('accounts_engaged'));
        $totalInteractionsValue = max(0, $getMetric('total_interactions'));
        $profileViewsValue = max(0, $getMetric('profile_views'));
        $profileActionsValue = max(0, $getMetric('profile_actions'));
        $followsValue = max(0, $getMetric('follows'));

        $viewFrequency = $reachValue > 0 ? round($viewsValue / $reachValue, 2) : 0;
        $engagementRate = $safeRate($totalInteractionsValue, $reachValue);
        $engagedAccountRate = $safeRate($engagedValue, $reachValue);
        $profileVisitRate = $safeRate($profileViewsValue, $reachValue);
        $profileActionRate = $safeRate($profileActionsValue, $profileViewsValue);
        $followConversionRate = $safeRate($followsValue, $profileViewsValue);

        $analysisCards = [
            [
                'label' => 'Frekuensi Tayangan',
                'value' => $formatDecimal($viewFrequency, 2) . '×',
                'description' => 'Rata-rata tayangan per akun yang dijangkau.',
                'accent' => 'blue',
            ],
            [
                'label' => 'Interaction Rate',
                'value' => $formatDecimal($engagementRate) . '%',
                'description' => 'Total interaksi dibandingkan jangkauan.',
                'accent' => 'rose',
            ],
            [
                'label' => 'Akun Terlibat',
                'value' => $formatDecimal($engagedAccountRate) . '%',
                'description' => 'Akun terlibat dibandingkan jangkauan.',
                'accent' => 'emerald',
            ],
            [
                'label' => 'Visit Rate Profil',
                'value' => $formatDecimal($profileVisitRate) . '%',
                'description' => 'Kunjungan profil dibandingkan jangkauan.',
                'accent' => 'violet',
            ],
            [
                'label' => 'Konversi Aksi Profil',
                'value' => $formatDecimal($profileActionRate) . '%',
                'description' => 'Aksi profil dibandingkan kunjungan profil.',
                'accent' => 'amber',
            ],
            [
                'label' => 'Konversi Follow',
                'value' => $formatDecimal($followConversionRate) . '%',
                'description' => 'Follow baru dibandingkan kunjungan profil.',
                'accent' => 'cyan',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | DONAT INTERAKSI
        |--------------------------------------------------------------------------
        |
        | total_interactions tidak selalu sama persis dengan jumlah komponen yang
        | berhasil diurai. Jika total Meta lebih besar, selisih ditampilkan sebagai
        | "Interaksi lain / penyesuaian Meta" agar donat tetap utuh.
        |
        | Jika jumlah komponen justru lebih besar daripada total Meta, donat memakai
        | jumlah komponen dan menampilkan peringatan cakupan metric berbeda.
        |
        */
        $interactionParts = [
            'likes' => [
                'label' => 'Likes',
                'color' => '#ef4444',
                'value' => $isAvailableStatus($getMetricStatus('likes')) ? max(0, $getMetric('likes')) : 0,
            ],
            'comments' => [
                'label' => 'Komentar',
                'color' => '#3b82f6',
                'value' => $isAvailableStatus($getMetricStatus('comments')) ? max(0, $getMetric('comments')) : 0,
            ],
            'shares' => [
                'label' => 'Dibagikan',
                'color' => '#f59e0b',
                'value' => $isAvailableStatus($getMetricStatus('shares')) ? max(0, $getMetric('shares')) : 0,
            ],
            'saves' => [
                'label' => 'Disimpan',
                'color' => '#8b5cf6',
                'value' => $isAvailableStatus($getMetricStatus('saves')) ? max(0, $getMetric('saves')) : 0,
            ],
            'replies' => [
                'label' => 'Balasan',
                'color' => '#64748b',
                'value' => $isAvailableStatus($getMetricStatus('replies')) ? max(0, $getMetric('replies')) : 0,
            ],
        ];

        $interactionComponentTotal = collect($interactionParts)->sum('value');
        $interactionMetaTotal = $isAvailableStatus($getMetricStatus('total_interactions'))
            ? max(0, $totalInteractionsValue)
            : 0;

        $interactionResidual = max($interactionMetaTotal - $interactionComponentTotal, 0);
        $interactionExcess = max($interactionComponentTotal - $interactionMetaTotal, 0);

        if ($interactionResidual > 0) {
            $interactionParts['other'] = [
                'label' => 'Interaksi lain / penyesuaian Meta',
                'color' => '#0f766e',
                'value' => $interactionResidual,
            ];
        }

        $donutTotal = $interactionMetaTotal > 0 && $interactionComponentTotal <= $interactionMetaTotal
            ? $interactionMetaTotal
            : $interactionComponentTotal;

        $donutStops = [];
        $runningPercent = 0.0;
        $positiveParts = collect($interactionParts)
            ->filter(fn(array $part): bool => (int) ($part['value'] ?? 0) > 0)
            ->values();

        foreach ($positiveParts as $index => $part) {
            $value = (int) ($part['value'] ?? 0);
            $start = $runningPercent;
            $segment = $donutTotal > 0 ? ($value / $donutTotal) * 100 : 0;
            $runningPercent += $segment;

            // Segmen terakhir dipaksa berakhir tepat di 100% untuk menghindari gap pembulatan.
            $end = $index === $positiveParts->count() - 1 ? 100 : min(100, $runningPercent);
            $donutStops[] = ($part['color'] ?? '#cbd5e1') . " {$start}% {$end}%";
        }

        $donutGradientString = !empty($donutStops)
            ? implode(', ', $donutStops)
            : '#e5e7eb 0% 100%';

        $interactionCoverage = $interactionMetaTotal > 0
            ? round(($interactionComponentTotal / $interactionMetaTotal) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | FUNNEL PROFIL
        |--------------------------------------------------------------------------
        */
        $funnelSteps = [
            [
                'label' => 'Akun Dijangkau',
                'value' => $reachValue,
                'rate' => 100,
                'rate_label' => 'Basis jangkauan',
                'color' => 'bg-blue-600',
            ],
            [
                'label' => 'Kunjungan Profil',
                'value' => $profileViewsValue,
                'rate' => min(100, $profileVisitRate),
                'rate_label' => $formatDecimal($profileVisitRate) . '% dari jangkauan',
                'color' => 'bg-violet-600',
            ],
            [
                'label' => 'Aksi Profil',
                'value' => $profileActionsValue,
                'rate' => min(100, $profileActionRate),
                'rate_label' => $formatDecimal($profileActionRate) . '% dari kunjungan profil',
                'color' => 'bg-amber-500',
            ],
            [
                'label' => 'Follow Baru',
                'value' => $followsValue,
                'rate' => min(100, $followConversionRate),
                'rate_label' => $formatDecimal($followConversionRate) . '% dari kunjungan profil',
                'color' => 'bg-emerald-600',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | CATATAN DEMOGRAFI
        |--------------------------------------------------------------------------
        */
        $demographicNotes = [
            'follower_demographics' =>
                '<strong>Siapa audiens yang sudah mengikuti akun?</strong><br>Gunakan data ini untuk mengecek kesesuaian pengikut saat ini dengan sasaran layanan dan wilayah kerja.',
            'reached_audience_demographics' =>
                '<strong>Siapa yang berhasil dijangkau konten?</strong><br>Mencakup follower dan non-follower yang terpapar konten sehingga cocok untuk membaca keberhasilan awareness.',
            'engaged_audience_demographics' =>
                '<strong>Siapa yang paling aktif berinteraksi?</strong><br>Menunjukkan kelompok audiens yang tidak hanya melihat, tetapi turut menyukai, berkomentar, membagikan, atau menyimpan konten.',
        ];
    @endphp

    {{--
    |--------------------------------------------------------------------------
    | LOADING OVERLAY
    |--------------------------------------------------------------------------
    --}}
    <div
        wire:loading.flex
        wire:target="loadInstagramData,fetchInstagramData,loadDemographicMetric"
        class="stat-loading-overlay"
    >
        <div class="flex max-w-md items-center gap-4 rounded-2xl bg-white px-6 py-5 shadow-2xl ring-1 ring-gray-950/10 dark:bg-gray-900 dark:ring-white/10">
            <div class="h-7 w-7 shrink-0 animate-spin rounded-full border-[3px] border-blue-600 border-t-transparent"></div>

            <div>
                <div class="text-sm font-bold text-gray-900 dark:text-white">Mengambil Data Meta</div>
                <div class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                    Request dapat memerlukan beberapa detik karena sejumlah metric diproses terpisah.
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        {{--
        |--------------------------------------------------------------------------
        | FILTER PERIODE
        |--------------------------------------------------------------------------
        --}}
        <form wire:submit="loadInstagramData">
            <x-filament::section icon="heroicon-o-calendar-days" icon-color="primary">
                <x-slot name="heading">Pilih Periode Statistik</x-slot>
                <x-slot name="description">
                    Atur periode utama dan pembanding, lalu klik <strong>Ambil Data</strong>. Perubahan filter tidak langsung meminta Meta API.
                </x-slot>

                <div class="space-y-5">
                    <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-4 dark:border-blue-900 dark:bg-blue-950/20">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                            <div class="flex-1 space-y-4">
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">Periode Utama</div>
                                    <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                        “Kemarin” mengikuti insight terakhir yang sudah lengkap, yaitu H-2.
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @foreach ($quickRanges as $key => $label)
                                        <button
                                            type="button"
                                            wire:click="applyQuickRange('{{ $key }}', 'current')"
                                            class="rounded-lg border px-3 py-2 text-xs font-semibold transition
                                                {{ $selectedPeriod === $key
                                                    ? 'border-blue-600 bg-blue-600 text-white shadow-sm'
                                                    : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-blue-800 dark:hover:bg-blue-950/30' }}"
                                        >
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto_1fr] sm:items-end">
                                    <label class="block">
                                        <span class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Tanggal awal</span>
                                        <input
                                            type="date"
                                            wire:model="customStartDate"
                                            class="w-full rounded-lg border border-blue-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-blue-900 dark:bg-gray-900 dark:text-white"
                                        >
                                    </label>

                                    <span class="pb-2 text-center text-xs font-bold uppercase tracking-wider text-blue-500">sampai</span>

                                    <label class="block">
                                        <span class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Tanggal akhir</span>
                                        <input
                                            type="date"
                                            wire:model="customEndDate"
                                            class="w-full rounded-lg border border-blue-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-blue-900 dark:bg-gray-900 dark:text-white"
                                        >
                                    </label>
                                </div>
                            </div>

                            <button
                                type="button"
                                wire:click="toggleComparison"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-bold shadow-sm transition
                                    {{ $isComparing
                                        ? 'border-purple-600 bg-purple-600 text-white hover:bg-purple-700'
                                        : 'border-gray-300 bg-white text-gray-700 hover:border-purple-300 hover:bg-purple-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-purple-800 dark:hover:bg-purple-950/30' }}"
                            >
                                <x-filament::icon icon="heroicon-o-arrows-right-left" class="h-5 w-5" />
                                {{ $isComparing ? 'Nonaktifkan Perbandingan' : 'Bandingkan Periode' }}
                            </button>
                        </div>
                    </div>

                    @if ($isComparing)
                        <div class="rounded-2xl border border-purple-200 bg-purple-50/70 p-4 dark:border-purple-900 dark:bg-purple-950/20">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-600 text-white shadow-sm">
                                            <x-filament::icon icon="heroicon-o-calendar-days" class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">Range Tanggal Pembanding</div>
                                            <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                                Pilih tanggal secara langsung. Tidak ada tombol periode cepat pada bagian pembanding.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto_1fr] sm:items-end">
                                        <label class="block">
                                            <span class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Tanggal awal pembanding</span>
                                            <input
                                                type="date"
                                                wire:model="compareStartDate"
                                                class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-purple-800 dark:bg-gray-900 dark:text-white"
                                            >
                                        </label>

                                        <span class="pb-2 text-center text-xs font-bold uppercase tracking-wider text-purple-500">sampai</span>

                                        <label class="block">
                                            <span class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Tanggal akhir pembanding</span>
                                            <input
                                                type="date"
                                                wire:model="compareEndDate"
                                                class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-purple-800 dark:bg-gray-900 dark:text-white"
                                            >
                                        </label>
                                    </div>
                                </div>

                                <div class="rounded-xl bg-white px-4 py-3 text-xs text-gray-600 shadow-sm ring-1 ring-purple-100 dark:bg-gray-900 dark:text-gray-300 dark:ring-purple-900">
                                    <div class="font-bold text-purple-700 dark:text-purple-300">Periode pembanding</div>
                                    <div class="mt-1">{{ $compareStartDate ?: '-' }} sampai {{ $compareEndDate ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col gap-4 border-t border-gray-200 pt-5 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $periodLabel }}</div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Tanggal pilihan: {{ $customStartDate ?: '-' }} sampai {{ $customEndDate ?: '-' }}
                            </div>

                            @if ($hasLoadedData && $lastLoadedAt)
                                <div class="mt-1 inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Terakhir dimuat: {{ $lastLoadedAt }}
                                </div>
                            @endif

                            <div wire:dirty class="mt-2 text-xs font-semibold text-amber-600 dark:text-amber-400">
                                Filter berubah. Klik Ambil Data untuk menerapkan perubahan.
                            </div>

                            @if ($filtersDirty && $hasLoadedData)
                                <div class="mt-2 text-xs font-semibold text-amber-600 dark:text-amber-400">
                                    Data yang tampil masih menggunakan filter sebelumnya.
                                </div>
                            @endif
                        </div>

                        <x-filament::button
                            type="submit"
                            size="lg"
                            color="primary"
                            icon="heroicon-o-cloud-arrow-down"
                            wire:loading.attr="disabled"
                            wire:target="loadInstagramData"
                        >
                            {{ $hasLoadedData ? 'Terapkan & Ambil Ulang Data' : 'Ambil Data' }}
                        </x-filament::button>
                    </div>

                    @if ($compareErrorMessage)
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-900/20 dark:text-red-300">
                            {{ $compareErrorMessage }}
                        </div>
                    @endif
                </div>
            </x-filament::section>
        </form>

        {{-- ERROR UTAMA --}}
        @if ($errorMessage)
            <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="danger">
                <x-slot name="heading">Statistik Instagram Tidak Dapat Dimuat</x-slot>

                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-300">
                    {{ $errorMessage }}
                </div>
            </x-filament::section>
        @endif

        {{-- BELUM ADA DATA --}}
        @if (!$hasLoadedData)
            <x-filament::section icon="heroicon-o-hand-raised" icon-color="warning">
                <x-slot name="heading">Data Belum Diambil</x-slot>
                <x-slot name="description">Halaman sengaja tidak meminta Meta API secara otomatis agar tetap ringan.</x-slot>

                <div class="stat-soft-grid rounded-2xl border border-dashed border-blue-300 bg-blue-50/70 p-8 text-center dark:border-blue-800 dark:bg-blue-950/20">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                        <x-filament::icon icon="heroicon-o-chart-pie" class="h-7 w-7" />
                    </div>
                    <div class="mt-4 text-base font-bold text-blue-900 dark:text-blue-100">Pilih periode lalu klik Ambil Data</div>
                    <div class="mx-auto mt-2 max-w-xl text-sm leading-6 text-blue-700 dark:text-blue-300">
                        Hasil untuk range yang sama dapat menggunakan cache sementara sehingga tidak selalu meminta ulang seluruh metric.
                    </div>
                </div>
            </x-filament::section>
        @endif

        @if ($hasLoadedData && !empty($igData))
            {{--
            |--------------------------------------------------------------------------
            | PROFIL AKUN
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-user-circle" icon-color="primary">
                <x-slot name="heading">Profil Akun Resmi</x-slot>
                <x-slot name="description">Kondisi profil ketika data terakhir dimuat.</x-slot>

                <x-slot name="afterHeader">
                    <x-filament::button
                        color="gray"
                        tag="a"
                        target="_blank"
                        href="https://instagram.com/{{ $igData['username'] ?? '' }}"
                        icon="heroicon-m-arrow-top-right-on-square"
                    >
                        Buka Instagram
                    </x-filament::button>
                </x-slot>

                <div class="stat-soft-grid rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-blue-50/50 p-5 dark:border-gray-800 dark:from-gray-900 dark:to-blue-950/20">
                    <div class="flex flex-col items-center gap-6 sm:flex-row">
                        <div class="shrink-0">
                            @if (!empty($igData['profile_picture_url']))
                                <img
                                    src="{{ $igData['profile_picture_url'] }}"
                                    alt="Foto profil Instagram"
                                    class="h-24 w-24 rounded-full object-cover shadow-xl ring-4 ring-white dark:ring-gray-800"
                                >
                            @else
                                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 text-3xl font-bold text-gray-400 shadow-xl ring-4 ring-white dark:bg-gray-800 dark:ring-gray-700">IG</div>
                            @endif
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <div class="text-2xl font-black tracking-tight text-gray-900 dark:text-white">
                                {{ '@' . ($igData['username'] ?? 'instagram') }}
                            </div>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Insight aktif: {{ $currentRangeLabel }}</div>

                            <div class="mt-5 grid grid-cols-2 gap-3 sm:max-w-lg">
                                <div class="rounded-xl bg-white/90 p-4 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900/80 dark:ring-gray-800">
                                    <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $formatNumber($igData['media_count'] ?? 0) }}</div>
                                    <div class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Total Postingan</div>
                                </div>
                                <div class="rounded-xl bg-white/90 p-4 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900/80 dark:ring-gray-800">
                                    <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $formatNumber($igData['followers_count'] ?? 0) }}</div>
                                    <div class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Pengikut Saat Ini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-filament::section>

            {{-- WARNING METRIC --}}
            @if (!empty($metricWarnings))
                <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="warning">
                    <x-slot name="heading">Catatan Ketersediaan Data</x-slot>
                    <x-slot name="description">Beberapa metric dapat dibatasi oleh akun, periode, atau tipe konten.</x-slot>

                    <details>
                        <summary class="cursor-pointer text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Lihat {{ count($metricWarnings) }} catatan metric
                        </summary>

                        <div class="stat-scrollbar mt-4 max-h-80 space-y-2 overflow-y-auto">
                            @foreach ($metricWarnings as $metricName => $warning)
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-900 dark:bg-amber-900/20">
                                    <div class="text-sm font-bold text-amber-800 dark:text-amber-300">{{ $metricName }}</div>
                                    <div class="mt-1 text-xs leading-5 text-amber-700 dark:text-amber-400">{{ $warning }}</div>
                                </div>
                            @endforeach
                        </div>
                    </details>
                </x-filament::section>
            @endif

            {{--
            |--------------------------------------------------------------------------
            | KPI UTAMA
            |--------------------------------------------------------------------------
            --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($mainCards as $metricKey)
                    @php
                        $definition = $metricDefinitions[$metricKey] ?? [];
                        $ui = $metricUi[$metricKey] ?? ['icon' => 'heroicon-o-chart-bar', 'color' => 'gray'];
                        $currentValue = $getMetric($metricKey);
                        $previousValue = $getPrevMetric($metricKey);
                        $currentStatus = $getMetricStatus($metricKey);
                        $previousStatus = $getPrevMetricStatus($metricKey);
                    @endphp

                    <x-filament::section :icon="$ui['icon']" :icon-color="$ui['color']">
                        <x-slot name="heading">{{ $definition['label'] ?? $metricKey }}</x-slot>

                        <div class="flex items-start justify-between gap-3">
                            <div class="text-3xl font-black tracking-tight {{ $isAvailableStatus($currentStatus) ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ $displayMetricValue($metricKey, $currentValue, $currentStatus) }}
                            </div>

                            <span class="inline-flex shrink-0 rounded-full px-2 py-1 text-[10px] font-bold ring-1 ring-inset {{ $statusClasses($currentStatus) }}">
                                {{ $statusLabel($currentStatus) }}
                            </span>
                        </div>

                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ $currentRangeLabel }}</div>

                        @if ($hasComparisonResult)
                            {!! $renderTrend($currentValue, $previousValue, $currentStatus, $previousStatus, true) !!}
                        @endif

                        <div class="mt-4 border-t border-gray-100 pt-3 text-xs leading-5 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            {{ $definition['description'] ?? '-' }}
                        </div>
                    </x-filament::section>
                @endforeach
            </div>

            {{--
            |--------------------------------------------------------------------------
            | RASIO KINERJA
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-calculator" icon-color="info">
                <x-slot name="heading">Rasio Kinerja</x-slot>
                <x-slot name="description">Indikator turunan dari data yang sudah dimuat; tidak menambah request Meta API.</x-slot>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
                    @foreach ($analysisCards as $card)
                        @php
                            $accent = $card['accent'] ?? 'blue';
                            $accentClasses = match ($accent) {
                                'rose' => 'from-rose-500 to-red-600 shadow-rose-500/20',
                                'emerald' => 'from-emerald-500 to-green-600 shadow-emerald-500/20',
                                'violet' => 'from-violet-500 to-purple-600 shadow-violet-500/20',
                                'amber' => 'from-amber-400 to-orange-500 shadow-amber-500/20',
                                'cyan' => 'from-cyan-500 to-sky-600 shadow-cyan-500/20',
                                default => 'from-blue-500 to-indigo-600 shadow-blue-500/20',
                            };
                        @endphp

                        <div class="stat-card overflow-hidden">
                            <div class="h-1.5 bg-gradient-to-r {{ $accentClasses }}"></div>
                            <div class="p-4">
                                <div class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $card['label'] }}</div>
                                <div class="mt-2 text-2xl font-black text-gray-900 dark:text-white">{{ $card['value'] }}</div>
                                <div class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $card['description'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{--
            |--------------------------------------------------------------------------
            | INTERAKSI DAN FUNNEL
            |--------------------------------------------------------------------------
            --}}
            <div class="grid grid-cols-1 gap-6 2xl:grid-cols-2">
                {{-- KOMPOSISI INTERAKSI --}}
                <x-filament::section icon="heroicon-o-heart" icon-color="danger">
                    <x-slot name="heading">Komposisi Interaksi Konten</x-slot>
                    <x-slot name="description">Donat memakai komponen interaksi yang tersedia dan diselaraskan dengan total Meta.</x-slot>

                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-3 text-xs leading-5 text-blue-800 dark:border-blue-900 dark:bg-blue-900/20 dark:text-blue-300">
                        <strong>Apa dasar total interaksi?</strong>
                        Total Interaksi berasal dari metric <code>total_interactions</code> Meta. Komponennya dapat mencakup Likes, Komentar, Shares, Saves, dan Replies. Nilainya tidak selalu sama persis dengan penjumlahan komponen karena cakupan metric, tipe konten, data yang tidak tersedia, serta penyesuaian Meta.
                    </div>

                    <div class="mt-5 grid grid-cols-1 items-center gap-7 lg:grid-cols-[220px_1fr]">
                        <div class="flex justify-center">
                            <div
                                class="relative h-52 w-52 rounded-full shadow-lg ring-1 ring-gray-200 dark:ring-gray-700"
                                style="background: conic-gradient({{ $donutGradientString }});"
                                aria-label="Grafik komposisi interaksi"
                            >
                                <div class="absolute inset-0 m-auto flex h-36 w-36 flex-col items-center justify-center rounded-full bg-white text-center shadow-inner dark:bg-gray-900">
                                    <span class="text-[11px] font-bold uppercase tracking-wide text-gray-400">Total Donat</span>
                                    <span class="mt-1 text-3xl font-black text-gray-900 dark:text-white">{{ $formatNumber($donutTotal) }}</span>
                                    <span class="mt-1 px-3 text-[10px] leading-4 text-gray-500 dark:text-gray-400">
                                        {{ $interactionResidual > 0 ? 'termasuk penyesuaian Meta' : 'komponen terurai' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-800">
                                    <div class="text-[10px] font-bold uppercase tracking-wide text-gray-400">Total Meta</div>
                                    <div class="mt-1 text-xl font-black text-gray-900 dark:text-white">{{ $formatNumber($interactionMetaTotal) }}</div>
                                </div>
                                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-800">
                                    <div class="text-[10px] font-bold uppercase tracking-wide text-gray-400">Komponen Terbaca</div>
                                    <div class="mt-1 text-xl font-black text-gray-900 dark:text-white">{{ $formatNumber($interactionComponentTotal) }}</div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-3">
                                @foreach ($interactionParts as $metricKey => $part)
                                    @php
                                        $partValue = (int) ($part['value'] ?? 0);
                                        $partPercent = $donutTotal > 0 ? round(($partValue / $donutTotal) * 100, 1) : 0;
                                    @endphp

                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <div class="flex min-w-0 items-center gap-2.5">
                                            <span class="h-3 w-3 shrink-0 rounded-full" style="background-color: {{ $part['color'] ?? '#cbd5e1' }};"></span>
                                            <span class="truncate font-medium text-gray-700 dark:text-gray-300">{{ $part['label'] ?? $metricKey }}</span>
                                        </div>

                                        <div class="shrink-0 text-right">
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $formatNumber($partValue) }}</span>
                                            <span class="ml-1 text-xs text-gray-400">({{ $formatDecimal($partPercent) }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($interactionMetaTotal > 0)
                                <div class="mt-4">
                                    <div class="mb-1.5 flex items-center justify-between text-xs">
                                        <span class="font-semibold text-gray-600 dark:text-gray-300">Komponen yang berhasil diurai</span>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $formatDecimal($interactionCoverage) }}%</span>
                                    </div>
                                    <div class="stat-progress">
                                        <span class="bg-gradient-to-r from-rose-500 to-purple-600" style="width: {{ min(100, $interactionCoverage) }}%;"></span>
                                    </div>
                                </div>
                            @endif

                            @if ($interactionExcess > 0)
                                <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800 dark:border-amber-900 dark:bg-amber-900/20 dark:text-amber-300">
                                    Jumlah komponen lebih besar {{ $formatNumber($interactionExcess) }} daripada total Meta. Hal ini menunjukkan cakupan atau metode agregasi metric tidak sepenuhnya sama. Donat memakai jumlah komponen agar tidak menghasilkan bagian negatif.
                                </div>
                            @endif
                        </div>
                    </div>
                </x-filament::section>

                {{-- FUNNEL PROFIL --}}
                <x-filament::section icon="heroicon-o-funnel" icon-color="primary">
                    <x-slot name="heading">Funnel dari Jangkauan ke Follow</x-slot>
                    <x-slot name="description">Membaca perjalanan audiens dari melihat konten hingga mengikuti akun.</x-slot>

                    <div class="space-y-4">
                        @foreach ($funnelSteps as $index => $step)
                            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-[11px] font-black text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $index + 1 }}</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $step['label'] }}</span>
                                        </div>
                                        <div class="mt-1 pl-8 text-xs text-gray-500 dark:text-gray-400">{{ $step['rate_label'] }}</div>
                                    </div>
                                    <div class="text-xl font-black text-gray-900 dark:text-white">{{ $formatNumber($step['value']) }}</div>
                                </div>

                                <div class="stat-progress mt-3">
                                    <span class="{{ $step['color'] }}" style="width: {{ max($step['value'] > 0 ? 2 : 0, min(100, $step['rate'])) }}%;"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 rounded-xl bg-gray-50 p-4 text-xs leading-5 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <strong>Catatan:</strong> funnel ini adalah analisis rasio, bukan pelacakan orang yang sama dari tahap ke tahap. Seorang pengguna dapat melihat konten berkali-kali atau melakukan lebih dari satu tindakan.
                    </div>
                </x-filament::section>
            </div>

            {{--
            |--------------------------------------------------------------------------
            | AKTIVITAS PROFIL
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-cursor-arrow-rays" icon-color="primary">
                <x-slot name="heading">Aktivitas dari Profil Instagram</x-slot>
                <x-slot name="description">Ringkasan kunjungan, klik website, dan tombol tindakan profil.</x-slot>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                    @foreach (['profile_views', 'profile_actions', 'website_clicks', 'profile_links_taps'] as $metricKey)
                        @php
                            $definition = $metricDefinitions[$metricKey] ?? [];
                            $value = $getMetric($metricKey);
                            $status = $getMetricStatus($metricKey);
                        @endphp

                        <div class="stat-card p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $definition['label'] ?? $metricKey }}</div>
                                    <div class="mt-1 text-[10px] font-medium uppercase tracking-wide text-gray-400">{{ $metricKey }}</div>
                                </div>
                                <span class="inline-flex rounded-full px-2 py-1 text-[10px] font-bold ring-1 ring-inset {{ $statusClasses($status) }}">{{ $statusLabel($status) }}</span>
                            </div>

                            <div class="mt-4 text-3xl font-black {{ $isAvailableStatus($status) ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ $displayMetricValue($metricKey, $value, $status) }}
                            </div>

                            <div class="mt-4 border-t border-gray-100 pt-3 text-xs leading-5 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                {{ $definition['description'] ?? '-' }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">Rincian Aksi Profil</div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Distribusi tombol yang berhasil diidentifikasi oleh Meta.</div>
                        </div>
                        <div class="text-sm font-black text-gray-900 dark:text-white">{{ $formatNumber($getMetric('profile_actions')) }} total aksi</div>
                    </div>

                    @if (!empty($profileActionsBreakdown))
                        @php
                            $profileActionsTotal = max(1, array_sum(array_map('intval', array_values($profileActionsBreakdown))));
                        @endphp

                        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                            @foreach ($profileActionsBreakdown as $label => $value)
                                @php
                                    $percentage = min(100, round(((int) $value / $profileActionsTotal) * 100, 1));
                                @endphp

                                <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="truncate text-xs font-semibold text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', $label) }}</div>
                                        <div class="text-sm font-black text-gray-900 dark:text-white">{{ $formatNumber($value) }}</div>
                                    </div>
                                    <div class="mt-2 flex items-center gap-3">
                                        <div class="stat-progress flex-1">
                                            <span class="bg-gradient-to-r from-blue-500 to-indigo-600" style="width: {{ $percentage }}%;"></span>
                                        </div>
                                        <span class="w-12 text-right text-[10px] font-bold text-gray-400">{{ $formatDecimal($percentage) }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            Meta belum memberikan rincian tombol profil pada periode ini.
                        </div>
                    @endif
                </div>
            </x-filament::section>

            {{--
            |--------------------------------------------------------------------------
            | PERTUMBUHAN PENGIKUT
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-user-group" icon-color="success">
                <x-slot name="heading">Pertumbuhan Pengikut</x-slot>
                <x-slot name="description">Follow Baru, Unfollow, dan pertumbuhan bersih pada periode aktif.</x-slot>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    @foreach (['follows', 'unfollows', 'net_follows'] as $metricKey)
                        @php
                            $definition = $metricDefinitions[$metricKey] ?? [];
                            $value = $getMetric($metricKey);
                            $status = $getMetricStatus($metricKey);
                        @endphp

                        <div class="stat-card p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $definition['label'] ?? $metricKey }}</div>
                                <span class="rounded-full px-2 py-1 text-[10px] font-bold ring-1 ring-inset {{ $statusClasses($status) }}">{{ $statusLabel($status) }}</span>
                            </div>

                            <div class="mt-3 text-3xl font-black text-gray-900 dark:text-white">{{ $displayMetricValue($metricKey, $value, $status) }}</div>
                            <div class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $definition['description'] ?? '-' }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    <strong>Rumus pertumbuhan bersih:</strong>
                    {{ $formatNumber($getMetric('follows')) }} Follow Baru
                    − {{ $formatNumber($getMetric('unfollows')) }} Unfollow
                    = <strong>{{ $getMetric('net_follows') > 0 ? '+' : '' }}{{ $formatNumber($getMetric('net_follows')) }}</strong>
                </div>
            </x-filament::section>

            {{--
            |--------------------------------------------------------------------------
            | FOLLOWER ONLINE
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-clock" icon-color="info">
                <x-slot name="heading">Waktu Follower Online</x-slot>
                <x-slot name="description">Distribusi waktu aktif follower berdasarkan data yang tersedia dari Meta.</x-slot>

                @if (!empty($onlineFollowersHourly))
                    @php
                        $maximumOnlineFollowers = max(1, ...array_map('intval', array_values($onlineFollowersHourly)));
                    @endphp

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-8">
                        @foreach ($onlineFollowersHourly as $hour => $value)
                            @php
                                $percentage = min(100, round(((int) $value / $maximumOnlineFollowers) * 100, 1));
                                $isPeakHour =
                                    $this->onlineFollowersPeakHour !== null
                                    && (int) $hour === (int) $this->onlineFollowersPeakHour;
                            @endphp

                            <div class="rounded-xl border p-3 {{ $isPeakHour ? 'border-blue-300 bg-blue-50 shadow-sm dark:border-blue-800 dark:bg-blue-900/20' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900' }}">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}.00</div>
                                    @if ($isPeakHour)
                                        <span class="rounded-full bg-blue-600 px-2 py-0.5 text-[8px] font-black tracking-wide text-white">PUNCAK</span>
                                    @endif
                                </div>

                                <div class="mt-2 text-xl font-black text-gray-900 dark:text-white">{{ $formatNumber($value) }}</div>
                                <div class="stat-progress mt-2 h-1.5">
                                    <span class="{{ $isPeakHour ? 'bg-blue-600' : 'bg-sky-400' }}" style="width: {{ $percentage }}%;"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center dark:border-gray-700">
                        <div class="text-sm font-bold text-gray-700 dark:text-gray-300">Distribusi jam tidak tersedia</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Meta dapat membatasi data ini berdasarkan akun dan periode terbaru.</div>
                    </div>
                @endif
            </x-filament::section>

            {{--
            |--------------------------------------------------------------------------
            | DETAIL METRIC
            |--------------------------------------------------------------------------
            --}}
            @foreach ($detailMetricGroups as $groupTitle => $groupMetrics)
                <x-filament::section icon="heroicon-o-squares-2x2" icon-color="primary">
                    <x-slot name="heading">{{ $groupTitle }}</x-slot>
                    <x-slot name="description">Detail metric pada {{ $currentRangeLabel }}.</x-slot>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($groupMetrics as $metricKey)
                            @php
                                $definition = $metricDefinitions[$metricKey] ?? [];
                                $currentValue = $getMetric($metricKey);
                                $previousValue = $getPrevMetric($metricKey);
                                $currentStatus = $getMetricStatus($metricKey);
                                $previousStatus = $getPrevMetricStatus($metricKey);
                            @endphp

                            <div class="stat-card p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $definition['label'] ?? $metricKey }}</div>
                                        <div class="mt-1 text-[10px] font-medium uppercase tracking-wide text-gray-400">{{ $metricKey }}</div>
                                    </div>

                                    <span class="inline-flex rounded-full px-2 py-1 text-[10px] font-bold ring-1 ring-inset {{ $statusClasses($currentStatus) }}">{{ $statusLabel($currentStatus) }}</span>
                                </div>

                                <div class="mt-4 text-2xl font-black {{ $isAvailableStatus($currentStatus) ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ $displayMetricValue($metricKey, $currentValue, $currentStatus) }}
                                </div>

                                @if ($metricKey === 'online_followers' && $this->onlineFollowersPeakHour !== null && $isAvailableStatus($currentStatus))
                                    <div class="mt-2 inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                        Jam puncak: {{ str_pad((string) $this->onlineFollowersPeakHour, 2, '0', STR_PAD_LEFT) }}.00
                                    </div>
                                @endif

                                @if ($hasComparisonResult)
                                    {!! $renderTrend($currentValue, $previousValue, $currentStatus, $previousStatus, true) !!}
                                @endif

                                <div class="mt-4 border-t border-gray-100 pt-3 text-xs leading-5 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                    <div><span class="font-bold text-gray-700 dark:text-gray-300">Sumber:</span> {{ $definition['source'] ?? '-' }}</div>
                                    <div class="mt-1">{{ $definition['description'] ?? '-' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endforeach

            {{--
            |--------------------------------------------------------------------------
            | DEMOGRAFI MANUAL
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-user-group" icon-color="success">
                <x-slot name="heading">Demografi Audiens</x-slot>
                <x-slot name="description">Demografi dimuat manual agar request utama tetap ringan.</x-slot>

                <div class="mb-5 flex flex-wrap gap-2">
                    <x-filament::button
                        size="sm"
                        color="success"
                        icon="heroicon-o-user-group"
                        wire:click="loadDemographicMetric('follower_demographics')"
                        wire:loading.attr="disabled"
                    >
                        Muat Demografi Follower
                    </x-filament::button>

                    <x-filament::button
                        size="sm"
                        color="primary"
                        icon="heroicon-o-eye"
                        wire:click="loadDemographicMetric('reached_audience_demographics')"
                        wire:loading.attr="disabled"
                    >
                        Muat Audiens Terjangkau
                    </x-filament::button>

                    <x-filament::button
                        size="sm"
                        color="warning"
                        icon="heroicon-o-heart"
                        wire:click="loadDemographicMetric('engaged_audience_demographics')"
                        wire:loading.attr="disabled"
                    >
                        Muat Audiens Terlibat
                    </x-filament::button>
                </div>

                @if ($this->demographicStatusMessage)
                    <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 p-3 text-sm text-blue-700 dark:border-blue-900 dark:bg-blue-900/20 dark:text-blue-300">
                        {{ $this->demographicStatusMessage }}
                    </div>
                @endif

                <div class="space-y-5">
                    @foreach ($demographicMetrics as $metricKey)
                        @php
                            $definition = $metricDefinitions[$metricKey] ?? [];
                            $demoData = $demographics[$metricKey] ?? [];
                            $note = $demographicNotes[$metricKey] ?? '';
                        @endphp

                        <div class="stat-card p-4">
                            <div class="text-base font-bold text-gray-900 dark:text-white">{{ $definition['label'] ?? $metricKey }}</div>
                            <div class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $definition['description'] ?? '-' }}</div>

                            <div class="mt-3 rounded-xl border border-indigo-100 bg-indigo-50 p-3 text-sm leading-6 text-indigo-800 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                {!! $note !!}
                            </div>

                            @if (empty($demoData))
                                <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    Data belum dimuat atau tidak tersedia.
                                </div>
                            @else
                                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                                    @foreach ($demoData as $breakdownName => $rows)
                                        @php
                                            $breakdownMax = max(1, ...collect($rows)->map(fn($row): int => (int) data_get($row, 'value', 0))->all());
                                        @endphp

                                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-800">
                                            <div class="mb-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ str_replace('_', ' ', $breakdownName) }}</div>

                                            <div class="space-y-3">
                                                @forelse ($rows as $row)
                                                    @php
                                                        $rowValue = (int) data_get($row, 'value', 0);
                                                        $rowWidth = min(100, round(($rowValue / $breakdownMax) * 100, 1));
                                                    @endphp

                                                    <div>
                                                        <div class="mb-1 flex items-center justify-between gap-3 text-xs">
                                                            <span class="truncate text-gray-700 dark:text-gray-300">{{ data_get($row, 'label', '-') }}</span>
                                                            <span class="font-bold text-gray-900 dark:text-white">{{ $formatNumber($rowValue) }}</span>
                                                        </div>
                                                        <div class="stat-progress h-1.5">
                                                            <span class="bg-indigo-500" style="width: {{ $rowWidth }}%;"></span>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-xs italic text-gray-400">Tidak ada data.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{--
            |--------------------------------------------------------------------------
            | PERBANDINGAN
            |--------------------------------------------------------------------------
            --}}
            @if ($hasComparisonResult)
                <x-filament::section icon="heroicon-o-arrows-right-left" icon-color="primary">
                    <x-slot name="heading">Detail Perbandingan</x-slot>
                    <x-slot name="description">{{ $currentRangeLabel }} dibandingkan dengan {{ $prevRangeLabel }}.</x-slot>

                    <div class="mb-6 rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white">Periode Utama vs Pembanding</div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Setiap baris memakai skala metricnya sendiri agar metric kecil tetap terlihat jelas.
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>{{ $currentRangeLabel }}</span>
                                <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-purple-600"></span>{{ $prevRangeLabel }}</span>
                            </div>
                        </div>

                        <div class="mt-5 space-y-5">
                            @foreach ($comparisonChartMetrics as $metricKey)
                                @php
                                    $definition = $metricDefinitions[$metricKey] ?? [];
                                    $currentValue = $getMetric($metricKey);
                                    $previousValue = $getPrevMetric($metricKey);
                                    $currentStatus = $getMetricStatus($metricKey);
                                    $previousStatus = $getPrevMetricStatus($metricKey);
                                    $rowMaximum = max(1, abs($currentValue), abs($previousValue));
                                    $currentWidth = $isAvailableStatus($currentStatus) ? min(100, round((abs($currentValue) / $rowMaximum) * 100, 1)) : 0;
                                    $previousWidth = $isAvailableStatus($previousStatus) ? min(100, round((abs($previousValue) / $rowMaximum) * 100, 1)) : 0;
                                @endphp

                                <div>
                                    <div class="mb-2 flex items-center justify-between gap-4">
                                        <div class="truncate text-xs font-bold text-gray-700 dark:text-gray-300">{{ $definition['label'] ?? $metricKey }}</div>
                                        <div class="flex shrink-0 items-center gap-3 text-[11px]">
                                            <span class="font-bold text-blue-700 dark:text-blue-300">{{ $displayMetricValue($metricKey, $currentValue, $currentStatus) }}</span>
                                            <span class="font-bold text-purple-700 dark:text-purple-300">{{ $displayMetricValue($metricKey, $previousValue, $previousStatus) }}</span>
                                        </div>
                                    </div>

                                    <div class="space-y-1.5">
                                        <div class="stat-progress">
                                            <span class="bg-blue-600" style="width: {{ $currentWidth }}%;"></span>
                                        </div>
                                        <div class="stat-progress">
                                            <span class="bg-purple-600" style="width: {{ $previousWidth }}%;"></span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="stat-scrollbar overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="whitespace-nowrap py-3 pr-4">Metric</th>
                                    <th class="whitespace-nowrap px-4 py-3 text-right">Periode Utama</th>
                                    <th class="whitespace-nowrap px-4 py-3 text-right">Pembanding</th>
                                    <th class="whitespace-nowrap px-4 py-3 text-right">Selisih</th>
                                    <th class="whitespace-nowrap py-3 pl-4 text-right">Perubahan</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($comparisonMetricKeys as $metricKey)
                                    @php
                                        $definition = $metricDefinitions[$metricKey] ?? [];
                                        $current = $getMetric($metricKey);
                                        $previous = $getPrevMetric($metricKey);
                                        $currentStatus = $getMetricStatus($metricKey);
                                        $previousStatus = $getPrevMetricStatus($metricKey);
                                        $canCompare = $isAvailableStatus($currentStatus) && $isAvailableStatus($previousStatus);
                                        $difference = $canCompare ? $current - $previous : null;
                                        $percentage = $canCompare
                                            ? ($previous > 0
                                                ? round(($difference / $previous) * 100, 1)
                                                : ($current > 0 ? 100 : 0))
                                            : null;
                                        $positive = $canCompare && $difference >= 0;
                                    @endphp

                                    <tr>
                                        <td class="py-3 pr-4">
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $definition['label'] ?? $metricKey }}</div>
                                            <div class="text-[10px] uppercase tracking-wide text-gray-400">{{ $metricKey }}</div>
                                        </td>

                                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">{{ $displayMetricValue($metricKey, $current, $currentStatus) }}</td>
                                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ $displayMetricValue($metricKey, $previous, $previousStatus) }}</td>

                                        <td class="px-4 py-3 text-right font-semibold {{ !$canCompare ? 'text-gray-400' : ($positive ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400') }}">
                                            @if ($canCompare)
                                                {{ $difference >= 0 ? '+' : '' }}{{ $formatNumber($difference) }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td class="py-3 pl-4 text-right font-semibold {{ !$canCompare ? 'text-gray-400' : ($positive ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400') }}">
                                            @if ($canCompare)
                                                {{ $positive ? '↑' : '↓' }} {{ abs($percentage) }}%
                                            @else
                                                Tidak tersedia
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @elseif ($isComparing && $filtersDirty)
                <x-filament::section icon="heroicon-o-information-circle" icon-color="warning">
                    <x-slot name="heading">Perbandingan Belum Diterapkan</x-slot>

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-900/20 dark:text-amber-300">
                        Range pembanding sudah dipilih. Klik <strong>Ambil Data</strong> agar grafik dan tabel perbandingan diperbarui.
                    </div>
                </x-filament::section>
            @endif

            {{-- CARA KERJA --}}
            <x-filament::section icon="heroicon-o-information-circle" icon-color="gray">
                <x-slot name="heading">Cara Membaca Dashboard</x-slot>

                <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="font-bold text-gray-900 dark:text-white">Total vs Komponen</div>
                        <div class="mt-2 leading-6 text-gray-600 dark:text-gray-300">Total Interaksi Meta dapat berbeda dari jumlah komponen. Selisih ditampilkan secara transparan pada donat.</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="font-bold text-gray-900 dark:text-white">Rasio Bukan Metric Meta</div>
                        <div class="mt-2 leading-6 text-gray-600 dark:text-gray-300">Rasio kinerja dihitung oleh sistem dari data yang sudah dimuat dan tidak menambah request API.</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="font-bold text-gray-900 dark:text-white">Filter Tidak Otomatis</div>
                        <div class="mt-2 leading-6 text-gray-600 dark:text-gray-300">Perubahan tanggal baru diterapkan setelah tombol Ambil Data ditekan.</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="font-bold text-gray-900 dark:text-white">Demografi Manual</div>
                        <div class="mt-2 leading-6 text-gray-600 dark:text-gray-300">Data demografi hanya diminta ketika tombol demografi ditekan agar halaman tetap ringan.</div>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- DEBUG --}}
        @if (!empty($this->debugData))
            <x-filament::section icon="heroicon-o-bug-ant" icon-color="danger">
                <x-slot name="heading">Debug Meta API</x-slot>

                <div class="stat-scrollbar max-h-[700px] space-y-3 overflow-y-auto pr-1">
                    @foreach ($this->debugData as $index => $debug)
                        <details class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                            <summary class="cursor-pointer">
                                <span class="font-bold text-gray-900 dark:text-white">#{{ $index + 1 }} — {{ $debug['title'] ?? 'Debug' }}</span>
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ $debug['time'] ?? '' }}</span>
                            </summary>

                            <pre class="stat-scrollbar mt-4 overflow-x-auto rounded-lg bg-gray-950 p-4 text-xs leading-relaxed text-green-300">{{ json_encode($debug['data'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                        </details>
                    @endforeach
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
