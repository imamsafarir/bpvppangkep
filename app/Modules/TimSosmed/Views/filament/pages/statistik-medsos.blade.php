<x-filament-panels::page>
    {{--
    |--------------------------------------------------------------------------
    | TAILWIND RUNTIME ENGINE & SCOPED STYLES
    |--------------------------------------------------------------------------
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
        /* Scoped Container */
        .ms-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
            font-family: inherit;
            width: 100%;
        }

        /* Scrollbars */
        .ms-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, .7) transparent;
        }

        .ms-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .ms-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .ms-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, .7);
            border-radius: 9999px;
        }

        .dark .ms-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, .85);
        }

        /* Loading overlay */
        .ms-loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(255, 255, 255, .82);
            backdrop-filter: blur(6px);
        }

        .dark .ms-loading-overlay {
            background: rgba(3, 7, 18, .88);
        }

        /* Cards */
        .ms-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .ms-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px -2px rgba(0, 0, 0, 0.06);
        }

        .dark .ms-card {
            border-color: #27272a;
            background: #18181b;
        }

        .dark .ms-card:hover {
            border-color: #3f3f46;
            box-shadow: 0 6px 16px -2px rgba(0, 0, 0, 0.35);
        }

        /* Brand Gradients */
        .ms-grad-ig {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        }

        .ms-grad-fb {
            background: linear-gradient(135deg, #1877F2 0%, #0d65d9 100%);
        }

        /* Hero Banner */
        .ms-hero-banner {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.04);
        }

        @media (min-width: 768px) {
            .ms-hero-banner {
                padding: 30px;
            }
        }

        .ms-hero-disconnected {
            background: linear-gradient(135deg, #eef2ff 0%, #ffffff 50%, #fdf2f8 100%);
            border: 1px solid #c7d2fe;
        }

        .dark .ms-hero-disconnected {
            background: linear-gradient(135deg, #0f172a 0%, #18181b 50%, #1e1b4b 100%);
            border-color: #3730a3;
        }

        .ms-hero-connected {
            background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 50%, #eff6ff 100%);
            border: 1px solid #a7f3d0;
        }

        .dark .ms-hero-connected {
            background: linear-gradient(135deg, #064e3b 0%, #18181b 50%, #0f172a 100%);
            border-color: #065f46;
        }

        /* Buttons */
        .ms-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            line-height: 1.25;
            white-space: nowrap;
        }

        .ms-btn-primary {
            background: #4f46e5;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
            border: 1px solid #4338ca;
        }

        .ms-btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .ms-btn-facebook {
            background: linear-gradient(135deg, #1877F2 0%, #0d65d9 100%);
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(24, 119, 242, 0.3);
            border: 1px solid #1565c0;
        }

        .ms-btn-facebook:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(24, 119, 242, 0.4);
        }

        .ms-btn-secondary {
            background: #ffffff;
            color: #334155 !important;
            border: 1px solid #cbd5e1;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .ms-btn-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }

        .dark .ms-btn-secondary {
            background: #27272a;
            color: #e2e8f0 !important;
            border-color: #3f3f46;
        }

        .dark .ms-btn-secondary:hover {
            background: #3f3f46;
        }

        .ms-btn-danger {
            background: #ef4444;
            color: #ffffff !important;
            border: 1px solid #dc2626;
        }

        .ms-btn-danger:hover {
            background: #dc2626;
        }

        /* Badges */
        .ms-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .ms-badge-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .dark .ms-badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.4);
        }

        .ms-badge-warning {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .dark .ms-badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.4);
        }

        .ms-badge-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .dark .ms-badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.4);
        }

        .ms-badge-info {
            background: #e0e7ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .dark .ms-badge-info {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
            border-color: rgba(99, 102, 241, 0.4);
        }

        .ms-badge-gray {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .dark .ms-badge-gray {
            background: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
            border-color: rgba(255, 255, 255, 0.12);
        }

        /* Grids */
        .ms-kpi-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            width: 100%;
        }

        @media (min-width: 640px) {
            .ms-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .ms-kpi-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .ms-ratio-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            width: 100%;
        }

        @media (min-width: 640px) {
            .ms-ratio-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .ms-ratio-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1536px) {
            .ms-ratio-grid {
                grid-template-columns: repeat(6, minmax(0, 1fr));
            }
        }

        .ms-two-cols {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            width: 100%;
        }

        @media (min-width: 1280px) {
            .ms-two-cols {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        /* Progress Bar */
        .ms-progress-bar {
            overflow: hidden;
            height: 8px;
            border-radius: 9999px;
            background: #e2e8f0;
            width: 100%;
        }

        .dark .ms-progress-bar {
            background: #374151;
        }

        .ms-progress-bar>span {
            display: block;
            height: 100%;
            border-radius: inherit;
            transition: width .5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Pulse Dot */
        .ms-pulse-dot {
            position: relative;
            display: inline-flex;
            height: 10px;
            width: 10px;
        }

        .ms-pulse-dot>span:first-child {
            position: absolute;
            display: inline-flex;
            height: 100%;
            width: 100%;
            border-radius: 9999px;
            background-color: #34d399;
            opacity: 0.75;
            animation: ms-ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        .ms-pulse-dot>span:last-child {
            position: relative;
            display: inline-flex;
            border-radius: 9999px;
            height: 10px;
            width: 10px;
            background-color: #10b981;
        }

        @keyframes ms-ping {

            75%,
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }
    </style>

    @php
        /*
        |--------------------------------------------------------------------------
        | DATA DASAR & HELPER
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

        $formatNumber = fn($value) => number_format((int) ($value ?? 0), 0, ',', '.');
        $formatDecimal = fn($value, int $decimal = 1) => number_format((float) ($value ?? 0), $decimal, ',', '.');

        $getMetric = fn(string $key): int => (int) data_get($metrics, $key, 0);
        $getPrevMetric = fn(string $key): int => (int) data_get($prevMetrics, $key, 0);
        $getMetricStatus = fn(string $key): string => (string) data_get($metricStatus, $key, 'pending');
        $getPrevMetricStatus = fn(string $key): string => (string) data_get($prevMetricStatus, $key, 'pending');

        $isAvailableStatus = fn(?string $status): bool => in_array($status, ['loaded', 'empty', 'derived'], true);

        $statusLabel = fn(?string $status): string => match ($status) {
            'loaded' => 'Tersedia',
            'empty' => 'Tidak ada aktivitas',
            'derived' => 'Hasil perhitungan',
            'unavailable' => 'Tidak tersedia',
            'error' => 'Gagal diambil',
            default => 'Belum dimuat',
        };

        $statusBadgeClass = fn(?string $status): string => match ($status) {
            'loaded' => 'ms-badge-success',
            'empty' => 'ms-badge-gray',
            'derived' => 'ms-badge-info',
            'error' => 'ms-badge-danger',
            'unavailable' => 'ms-badge-warning',
            default => 'ms-badge-gray',
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
                return '<div style="font-size: 11px; color: #94a3b8; font-weight: 500;">Perbandingan tidak tersedia</div>';
            }

            $difference = $current - $previous;
            $percentage = $previous > 0 ? round(($difference / $previous) * 100, 1) : ($current > 0 ? 100 : 0);

            $positive = $difference >= 0;
            $color = $positive ? '#16a34a' : '#dc2626';
            $symbol = $positive ? '↑' : '↓';

            return '<div style="font-size: 12.5px; font-weight: 700; color: ' .
                $color .
                '; display: flex; align-items: center; gap: 4px;">' .
                $symbol .
                ' ' .
                abs($percentage) .
                '% ' .
                '<span style="font-size: 11px; font-weight: normal; color: #64748b;">vs ' .
                $formatNumber($previous) .
                '</span></div>';
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

        $demographicMetrics = [
            'follower_demographics',
            'reached_audience_demographics',
            'engaged_audience_demographics',
        ];

        $comparisonMetricKeys = collect($metricDefinitions)
            ->filter(function ($definition, $key) {
                return in_array($definition['type'] ?? '', ['number', 'derived'], true) &&
                    !in_array($key, ['follower_count', 'follows_and_unfollows'], true);
            })
            ->keys()
            ->values()
            ->all();

        $hasComparisonResult = $isComparing && filled($prevRangeLabel) && !$compareErrorMessage && !$filtersDirty;

        /*
        |--------------------------------------------------------------------------
        | ANALISIS RASIO
        |--------------------------------------------------------------------------
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
                'description' => 'Rata-rata tayangan per akun unik yang dijangkau.',
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

        if ($interactionResidual > 0) {
            $interactionParts['other'] = [
                'label' => 'Interaksi lain / penyesuaian Meta',
                'color' => '#0f766e',
                'value' => $interactionResidual,
            ];
        }

        $donutTotal =
            $interactionMetaTotal > 0 && $interactionComponentTotal <= $interactionMetaTotal
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

            $end = $index === $positiveParts->count() - 1 ? 100 : min(100, $runningPercent);
            $donutStops[] = ($part['color'] ?? '#cbd5e1') . " {$start}% {$end}%";
        }

        $donutGradientString = !empty($donutStops) ? implode(', ', $donutStops) : '#e5e7eb 0% 100%';

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
                'color' => '#3b82f6',
            ],
            [
                'label' => 'Kunjungan Profil',
                'value' => $profileViewsValue,
                'rate' => min(100, $profileVisitRate),
                'rate_label' => $formatDecimal($profileVisitRate) . '% dari jangkauan',
                'color' => '#8b5cf6',
            ],
            [
                'label' => 'Aksi Profil',
                'value' => $profileActionsValue,
                'rate' => min(100, $profileActionRate),
                'rate_label' => $formatDecimal($profileActionRate) . '% dari kunjungan profil',
                'color' => '#f59e0b',
            ],
            [
                'label' => 'Follow Baru',
                'value' => $followsValue,
                'rate' => min(100, $followConversionRate),
                'rate_label' => $formatDecimal($followConversionRate) . '% dari kunjungan profil',
                'color' => '#10b981',
            ],
        ];

        $isConnected = $this->isConnected();
        $connectedSetting = $this->getConnectedSetting();
        $configuredIgId = $connectedSetting?->ig_user_id ?? ($igData['id'] ?? null);
        $igUsername = $igData['username'] ?? 'bpvppangkep';
    @endphp

    {{--
    |--------------------------------------------------------------------------
    | LOADING OVERLAY
    |--------------------------------------------------------------------------
    --}}
    <div wire:loading.flex wire:target="loadInstagramData,fetchInstagramData,loadDemographicMetric"
        class="ms-loading-overlay">
        <div class="ms-card"
            style="padding: 24px 32px; display: flex; align-items: center; gap: 18px; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
            <div
                style="width: 32px; height: 32px; border: 4px solid #4f46e5; border-top-color: transparent; border-radius: 9999px; animation: spin 1s linear infinite; flex-shrink: 0;">
            </div>
            <div>
                <div style="font-size: 15px; font-weight: 800; color: #0f172a;" class="dark:text-white">Mengambil Data
                    Meta API</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 3px;" class="dark:text-gray-400">
                    Meminta wawasan Instagram @bpvppangkep langsung dari Meta Graph API v25.0...
                </div>
            </div>
        </div>
    </div>
    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="ms-wrap">
        {{--
        |--------------------------------------------------------------------------
        | 1. HERO BANNER KONEKSI FACEBOOK BUSINESS / META API
        |--------------------------------------------------------------------------
        --}}
        @if (!$isConnected)
            {{-- BANNER ONBOARDING (BELUM TERHUBUNG) --}}
            <div class="ms-hero-banner ms-hero-disconnected">
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div
                        style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 16px; max-width: 720px;">
                            <div class="ms-grad-ig"
                                style="width: 64px; height: 64px; border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 16px rgba(225, 48, 108, 0.25); color: #ffffff;">
                                {{-- Instagram SVG Icon --}}
                                <svg style="width: 34px; height: 34px; fill: currentColor;" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg>
                            </div>

                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span class="ms-badge ms-badge-warning">
                                        <span
                                            style="width: 6px; height: 6px; border-radius: 9999px; background: #f59e0b;"></span>
                                        Belum Terhubung
                                    </span>
                                    <span style="font-size: 11.5px; font-weight: 600; color: #64748b;"
                                        class="dark:text-gray-400">Meta Graph API v25.0</span>
                                </div>

                                <h2 style="font-size: 22px; font-weight: 900; color: #0f172a; margin-top: 6px; letter-spacing: -0.4px;"
                                    class="dark:text-white">
                                    Hubungkan Akun Instagram Resmi <span style="color: #db2777;">@bpvppangkep</span>
                                </h2>
                                <p style="font-size: 13px; color: #475569; margin-top: 4px; line-height: 1.5;"
                                    class="dark:text-gray-300">
                                    Tautkan sistem dengan Facebook Business untuk menarik data analitik insight
                                    real-time secara otomatis: jangkauan, impresi, interaksi, kunjungan profil, hingga
                                    demografi pengikut BPVP Pangkep.
                                </p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <a href="{{ route('facebook.login') }}" class="ms-btn ms-btn-facebook">
                                <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                                <span>Hubungkan via Facebook Business</span>
                            </a>
                        </div>
                    </div>

                    {{-- 3 Langkah Panduan --}}
                    <div style="border-top: 1px solid rgba(199, 210, 254, 0.6); padding-top: 16px; margin-top: 8px;">
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                            <div class="ms-card"
                                style="padding: 10px 14px; display: flex; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.7);"
                                class="dark:bg-gray-800">
                                <span
                                    style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 8px; background: #fce7f3; color: #be185d; font-size: 12px; font-weight: 800;">1</span>
                                <span style="font-size: 12px; font-weight: 600; color: #334155;"
                                    class="dark:text-gray-300">Akun IG @bpvppangkep tipe Bisnis</span>
                            </div>
                            <div class="ms-card"
                                style="padding: 10px 14px; display: flex; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.7);"
                                class="dark:bg-gray-800">
                                <span
                                    style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 8px; background: #e0e7ff; color: #4338ca; font-size: 12px; font-weight: 800;">2</span>
                                <span style="font-size: 12px; font-weight: 600; color: #334155;"
                                    class="dark:text-gray-300">Tertaut ke Fanspage FB BPVP Pangkep</span>
                            </div>
                            <div class="ms-card"
                                style="padding: 10px 14px; display: flex; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.7);"
                                class="dark:bg-gray-800">
                                <span
                                    style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 8px; background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 800;">3</span>
                                <span style="font-size: 12px; font-weight: 600; color: #334155;"
                                    class="dark:text-gray-300">Berikan izin wawasan saat login</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- BANNER HERO (TERHUBUNG) --}}
            <div class="ms-hero-banner ms-hero-connected">
                <div style="display: flex; flex-direction: column; gap: 18px;">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 18px;">
                        <div style="display: flex; align-items: center; gap: 18px;">
                            <div style="position: relative; flex-shrink: 0;">
                                @if (!empty($igData['profile_picture_url']))
                                    <div class="ms-grad-ig"
                                        style="padding: 3px; border-radius: 9999px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                        <img src="{{ $igData['profile_picture_url'] }}" alt="Instagram @bpvppangkep"
                                            style="width: 64px; height: 64px; border-radius: 9999px; object-fit: cover; border: 2px solid #ffffff;">
                                    </div>
                                @else
                                    <div class="ms-grad-ig"
                                        style="width: 68px; height: 68px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 22px; font-weight: 900; border: 3px solid #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                        BP
                                    </div>
                                @endif
                                <span style="position: absolute; bottom: 2px; right: 2px;">
                                    <span class="ms-pulse-dot">
                                        <span></span>
                                        <span></span>
                                    </span>
                                </span>
                            </div>

                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span class="ms-badge ms-badge-success">
                                        <span
                                            style="width: 6px; height: 6px; border-radius: 9999px; background: #16a34a;"></span>
                                        Terhubung Aktif ke Meta API v25.0
                                    </span>
                                    @if ($configuredIgId)
                                        <span class="ms-badge ms-badge-gray" style="font-family: monospace;">ID:
                                            {{ $configuredIgId }}</span>
                                    @endif
                                </div>

                                <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                                    <h2 style="font-size: 24px; font-weight: 900; color: #0f172a; letter-spacing: -0.4px;"
                                        class="dark:text-white">
                                        {{ '@' . $igUsername }}
                                    </h2>
                                    <a href="https://instagram.com/{{ $igUsername }}" target="_blank"
                                        style="color: #64748b; transition: color 0.2s;" title="Buka Instagram">
                                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="h-5 w-5" />
                                    </a>
                                </div>

                                <div style="font-size: 12px; color: #64748b; margin-top: 2px;"
                                    class="dark:text-gray-400">
                                    <span>Akun Resmi Balai Pelatihan Vokasi dan Produktivitas Pangkep</span>
                                    @if ($lastLoadedAt)
                                        <span style="margin: 0 6px;">•</span>
                                        <span style="color: #16a34a; font-weight: 700;">Terakhir Disinkronkan:
                                            {{ $lastLoadedAt }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <button type="button" wire:click="loadInstagramData" class="ms-btn ms-btn-primary"
                                wire:loading.attr="disabled" wire:target="loadInstagramData">
                                <x-filament::icon icon="heroicon-o-cloud-arrow-down" class="h-4 w-4" />
                                <span>{{ $hasLoadedData ? 'Ambil Ulang Data' : 'Ambil Data' }}</span>
                            </button>

                            <button type="button" wire:click="mountAction('disconnect_facebook')"
                                class="ms-btn ms-btn-danger">
                                <x-filament::icon icon="heroicon-o-link-slash" class="h-4 w-4" />
                                <span>Putuskan</span>
                            </button>
                        </div>
                    </div>

                    @if (!empty($igData))
                        <div
                            style="border-top: 1px solid rgba(167, 243, 208, 0.6); padding-top: 14px; margin-top: 4px;">
                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                                <div class="ms-card" style="padding: 12px 16px; background: rgba(255, 255, 255, 0.8);"
                                    class="dark:bg-gray-800">
                                    <div
                                        style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                        Pengikut Saat Ini</div>
                                    <div style="font-size: 20px; font-weight: 900; color: #0f172a; margin-top: 2px;"
                                        class="dark:text-white">{{ $formatNumber($igData['followers_count'] ?? 0) }}
                                    </div>
                                </div>
                                <div class="ms-card" style="padding: 12px 16px; background: rgba(255, 255, 255, 0.8);"
                                    class="dark:bg-gray-800">
                                    <div
                                        style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                        Total Postingan</div>
                                    <div style="font-size: 20px; font-weight: 900; color: #0f172a; margin-top: 2px;"
                                        class="dark:text-white">{{ $formatNumber($igData['media_count'] ?? 0) }}</div>
                                </div>
                                <div class="ms-card" style="padding: 12px 16px; background: rgba(255, 255, 255, 0.8);"
                                    class="dark:bg-gray-800">
                                    <div
                                        style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                        Periode Aktif</div>
                                    <div style="font-size: 13.5px; font-weight: 800; color: #0f172a; margin-top: 4px;"
                                        class="dark:text-white">{{ $currentRangeLabel ?: 'Belum dipilih' }}</div>
                                </div>
                                <div class="ms-card" style="padding: 12px 16px; background: rgba(255, 255, 255, 0.8);"
                                    class="dark:bg-gray-800">
                                    <div
                                        style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                        Status Koneksi</div>
                                    <div
                                        style="font-size: 13px; font-weight: 800; color: #16a34a; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                                        <span
                                            style="width: 8px; height: 8px; border-radius: 9999px; background: #16a34a;"></span>
                                        Token Aktif & Valid
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{--
        |--------------------------------------------------------------------------
        | 2. FILTER PERIODE WAKTU
        |--------------------------------------------------------------------------
        --}}
        <form wire:submit="loadInstagramData">
            <x-filament::section icon="heroicon-o-calendar-days" icon-color="primary">
                <x-slot name="heading">Pilih Periode Statistik</x-slot>
                <x-slot name="description">
                    Atur periode waktu utama dan opsi perbandingan, lalu klik <strong>Ambil Data</strong>.
                </x-slot>

                <div style="display: flex; flex-direction: column; gap: 18px;">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px;"
                        class="dark:bg-gray-800 dark:border-gray-700">
                        <div style="display: flex; flex-direction: column; gap: 14px;">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                                <div>
                                    <div style="font-size: 14px; font-weight: 800; color: #0f172a;"
                                        class="dark:text-white">Periode Utama</div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                        “Kemarin” mengikuti data insight terakhir yang sudah lengkap dari Meta (H-2).
                                    </div>
                                </div>

                                <button type="button" wire:click="toggleComparison"
                                    class="ms-btn {{ $isComparing ? 'ms-btn-primary' : 'ms-btn-secondary' }}"
                                    style="padding: 7px 14px; font-size: 12px;">
                                    <x-filament::icon icon="heroicon-o-arrows-right-left" class="h-4 w-4" />
                                    <span>{{ $isComparing ? 'Nonaktifkan Perbandingan' : 'Bandingkan Periode' }}</span>
                                </button>
                            </div>

                            {{-- Tombol Periode Cepat --}}
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                @foreach ($quickRanges as $key => $label)
                                    <button type="button"
                                        wire:click="applyQuickRange('{{ $key }}', 'current')"
                                        class="ms-btn {{ $selectedPeriod === $key ? 'ms-btn-primary' : 'ms-btn-secondary' }}"
                                        style="padding: 6px 14px; font-size: 12px;">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- Input Tanggal --}}
                            <div
                                style="display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap; margin-top: 4px;">
                                <div style="flex: 1; min-width: 170px;">
                                    <label
                                        style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 4px;">Tanggal
                                        Awal</label>
                                    <input type="date" wire:model="customStartDate"
                                        style="width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 12px; font-size: 13px; background: #ffffff; color: #1e293b;"
                                        class="dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>

                                <span
                                    style="font-size: 12px; font-weight: 700; color: #6366f1; text-transform: uppercase; padding-bottom: 10px;">sampai</span>

                                <div style="flex: 1; min-width: 170px;">
                                    <label
                                        style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 4px;">Tanggal
                                        Akhir</label>
                                    <input type="date" wire:model="customEndDate"
                                        style="width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 12px; font-size: 13px; background: #ffffff; color: #1e293b;"
                                        class="dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Mode Bandingkan --}}
                    @if ($isComparing)
                        <div style="background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 14px; padding: 18px;"
                            class="dark:bg-purple-950/20 dark:border-purple-900">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                                <div>
                                    <div style="font-size: 14px; font-weight: 800; color: #581c87;"
                                        class="dark:text-purple-300">Range Tanggal Pembanding</div>
                                    <div style="font-size: 12px; color: #7e22ce; margin-top: 2px;"
                                        class="dark:text-purple-400">
                                        Tentukan periode pembanding untuk membaca tren kenaikan atau penurunan performa.
                                    </div>
                                </div>

                                <div class="ms-badge"
                                    style="background: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe;">
                                    {{ $compareStartDate ?: '-' }} sampai {{ $compareEndDate ?: '-' }}
                                </div>
                            </div>

                            <div
                                style="display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap; margin-top: 12px;">
                                <div style="flex: 1; min-width: 170px;">
                                    <label
                                        style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #7e22ce; margin-bottom: 4px;"
                                        class="dark:text-purple-300">Tanggal Awal Pembanding</label>
                                    <input type="date" wire:model="compareStartDate"
                                        style="width: 100%; border: 1px solid #d8b4fe; border-radius: 10px; padding: 8px 12px; font-size: 13px; background: #ffffff; color: #1e293b;"
                                        class="dark:bg-gray-900 dark:border-purple-800 dark:text-white">
                                </div>

                                <span
                                    style="font-size: 12px; font-weight: 700; color: #9333ea; text-transform: uppercase; padding-bottom: 10px;">sampai</span>

                                <div style="flex: 1; min-width: 170px;">
                                    <label
                                        style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #7e22ce; margin-bottom: 4px;"
                                        class="dark:text-purple-300">Tanggal Akhir Pembanding</label>
                                    <input type="date" wire:model="compareEndDate"
                                        style="width: 100%; border: 1px solid #d8b4fe; border-radius: 10px; padding: 8px 12px; font-size: 13px; background: #ffffff; color: #1e293b;"
                                        class="dark:bg-gray-900 dark:border-purple-800 dark:text-white">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Tombol Submit & Indikator --}}
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; border-top: 1px solid #f1f5f9; padding-top: 14px;"
                        class="dark:border-gray-800">
                        <div>
                            <div style="font-size: 13.5px; font-weight: 800; color: #1e293b;"
                                class="dark:text-gray-200">{{ $periodLabel }}</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                Periode aktif: {{ $customStartDate ?: '-' }} sampai {{ $customEndDate ?: '-' }}
                            </div>

                            @if ($hasLoadedData && $lastLoadedAt)
                                <div
                                    style="font-size: 11.5px; font-weight: 700; color: #16a34a; margin-top: 4px; display: flex; align-items: center; gap: 5px;">
                                    <span
                                        style="width: 6px; height: 6px; border-radius: 9999px; background: #16a34a;"></span>
                                    Terakhir dimuat: {{ $lastLoadedAt }}
                                </div>
                            @endif

                            <div wire:dirty
                                style="font-size: 11.5px; font-weight: 700; color: #d97706; margin-top: 4px;">
                                ⚠️ Filter diubah. Klik Ambil Data untuk menerapkan filter baru.
                            </div>
                        </div>

                        <button type="submit" class="ms-btn ms-btn-primary"
                            style="padding: 12px 24px; font-size: 14px;" wire:loading.attr="disabled"
                            wire:target="loadInstagramData">
                            <x-filament::icon icon="heroicon-o-cloud-arrow-down" class="h-5 w-5" />
                            <span>{{ $hasLoadedData ? 'Terapkan & Ambil Ulang Data' : 'Ambil Data' }}</span>
                        </button>
                    </div>

                    @if ($compareErrorMessage)
                        <div
                            style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #b91c1c;">
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

                <div
                    style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; font-size: 13.5px; line-height: 1.5; color: #b91c1c;">
                    {{ $errorMessage }}
                </div>
            </x-filament::section>
        @endif

        {{-- STATUS BELUM AMBIL DATA --}}
        @if (!$hasLoadedData)
            <x-filament::section icon="heroicon-o-hand-raised" icon-color="warning">
                <x-slot name="heading">Data Belum Diambil</x-slot>
                <x-slot name="description">Halaman sengaja tidak meminta Meta API secara otomatis agar tetap
                    ringan.</x-slot>

                <div style="border: 2px dashed #93c5fd; background: #eff6ff; border-radius: 16px; padding: 36px 20px; text-align: center;"
                    class="dark:bg-blue-950/20 dark:border-blue-900">
                    <div
                        style="width: 56px; height: 56px; border-radius: 16px; background: #2563eb; color: #ffffff; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 4px 12px rgba(37,99,235,0.3);">
                        <x-filament::icon icon="heroicon-o-chart-pie" class="h-7 w-7" />
                    </div>
                    <div style="margin-top: 14px; font-size: 16px; font-weight: 800; color: #1e3a8a;"
                        class="dark:text-blue-200">Pilih periode lalu klik Ambil Data</div>
                    <div style="margin-top: 4px; font-size: 13px; color: #3b82f6; max-width: 540px; margin-left: auto; margin-right: auto;"
                        class="dark:text-blue-300">
                        Data akan ditarik langsung dari Meta Graph API v25.0 untuk akun resmi Instagram @bpvppangkep.
                    </div>
                    @if ($isConnected)
                        <div style="margin-top: 18px;">
                            <button type="button" wire:click="loadInstagramData" class="ms-btn ms-btn-primary"
                                style="padding: 10px 20px;">
                                <x-filament::icon icon="heroicon-o-cloud-arrow-down" class="h-5 w-5" />
                                <span>Ambil Data Sekarang</span>
                            </button>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @endif

        {{-- DASHBOARD KETIKA SUDAH DIMUAT --}}
        @if ($hasLoadedData && !empty($igData))
            {{-- WARNING METRIC --}}
            @if (!empty($metricWarnings))
                <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="warning">
                    <x-slot name="heading">Catatan Ketersediaan Data</x-slot>
                    <x-slot name="description">Beberapa metric dibatasi oleh akun atau kebijakan Meta API.</x-slot>

                    <details>
                        <summary style="cursor: pointer; font-size: 13px; font-weight: 700; color: #475569;"
                            class="dark:text-gray-300">
                            Lihat {{ count($metricWarnings) }} catatan metric
                        </summary>

                        <div class="ms-scrollbar"
                            style="max-height: 240px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; margin-top: 12px;">
                            @foreach ($metricWarnings as $metricName => $warning)
                                <div
                                    style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; padding: 10px 14px;">
                                    <div style="font-size: 12px; font-weight: 800; color: #92400e;">
                                        {{ $metricName }}</div>
                                    <div style="font-size: 11.5px; color: #b45309; margin-top: 2px;">
                                        {{ $warning }}</div>
                                </div>
                            @endforeach
                        </div>
                    </details>
                </x-filament::section>
            @endif

            {{--
            |--------------------------------------------------------------------------
            | 3. KARTU KPI UTAMA (8 METRIC)
            |--------------------------------------------------------------------------
            --}}
            <div class="ms-kpi-grid">
                @foreach ($mainCards as $metricKey)
                    @php
                        $definition = $metricDefinitions[$metricKey] ?? [];
                        $ui = $metricUi[$metricKey] ?? ['icon' => 'heroicon-o-chart-bar', 'color' => 'gray'];
                        $currentValue = $getMetric($metricKey);
                        $previousValue = $getPrevMetric($metricKey);
                        $currentStatus = $getMetricStatus($metricKey);
                        $previousStatus = $getPrevMetricStatus($metricKey);

                        $accentColor = match ($metricKey) {
                            'views', 'reach' => '#3b82f6',
                            'accounts_engaged', 'follows', 'net_follows' => '#10b981',
                            'total_interactions', 'likes' => '#ef4444',
                            'comments', 'online_followers' => '#06b6d4',
                            'shares', 'website_clicks' => '#f59e0b',
                            'profile_views', 'profile_actions', 'saves' => '#8b5cf6',
                            'unfollows' => '#dc2626',
                            default => '#6366f1',
                        };
                    @endphp

                    <div class="ms-card"
                        style="padding: 18px 20px; display: flex; flex-direction: column; justify-content: space-between; border-left: 4px solid {{ $accentColor }};">
                        <div>
                            <div
                                style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;">
                                <span style="font-size: 13px; font-weight: 800; color: #475569;"
                                    class="dark:text-gray-300">
                                    {{ $definition['label'] ?? $metricKey }}
                                </span>
                                <span class="ms-badge {{ $statusBadgeClass($currentStatus) }}">
                                    {{ $statusLabel($currentStatus) }}
                                </span>
                            </div>

                            <div style="margin-top: 14px; font-size: 32px; font-weight: 900; letter-spacing: -0.5px; line-height: 1; color: #0f172a;"
                                class="dark:text-white">
                                {{ $displayMetricValue($metricKey, $currentValue, $currentStatus) }}
                            </div>

                            <div style="margin-top: 6px; font-size: 11.5px; font-weight: 500; color: #64748b;"
                                class="dark:text-gray-400">
                                {{ $currentRangeLabel ?: 'Periode aktif' }}
                            </div>

                            @if ($hasComparisonResult)
                                <div style="margin-top: 8px;">
                                    {!! $renderTrend($currentValue, $previousValue, $currentStatus, $previousStatus, true) !!}
                                </div>
                            @endif
                        </div>

                        <div style="margin-top: 14px; padding-top: 10px; border-top: 1px solid #f1f5f9; font-size: 11px; color: #94a3b8; line-height: 1.4;"
                            class="dark:border-gray-800 dark:text-gray-400">
                            {{ $definition['description'] ?? '-' }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{--
            |--------------------------------------------------------------------------
            | 4. RASIO KINERJA (6 METRIC TURUNAN)
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-calculator" icon-color="info">
                <x-slot name="heading">Rasio Kinerja & Efektivitas</x-slot>
                <x-slot name="description">Indikator turunan dari data yang sudah dimuat; dihitung lokal tanpa menambah
                    request Meta API.</x-slot>

                <div class="ms-ratio-grid">
                    @foreach ($analysisCards as $card)
                        @php
                            $accent = $card['accent'] ?? 'blue';
                            $barColor = match ($accent) {
                                'rose' => '#f43f5e',
                                'emerald' => '#10b981',
                                'violet' => '#8b5cf6',
                                'amber' => '#f59e0b',
                                'cyan' => '#06b6d4',
                                default => '#3b82f6',
                            };
                        @endphp

                        <div class="ms-card" style="overflow: hidden;">
                            <div style="height: 4px; background: {{ $barColor }}; width: 100%;"></div>
                            <div style="padding: 16px;">
                                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b;"
                                    class="dark:text-gray-400">
                                    {{ $card['label'] }}
                                </div>
                                <div style="margin-top: 8px; font-size: 26px; font-weight: 900; color: #0f172a;"
                                    class="dark:text-white">
                                    {{ $card['value'] }}
                                </div>
                                <div style="margin-top: 6px; font-size: 11.5px; line-height: 1.4; color: #64748b;"
                                    class="dark:text-gray-400">
                                    {{ $card['description'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{--
            |--------------------------------------------------------------------------
            | 5. DONAT INTERAKSI & FUNNEL PROFIL
            |--------------------------------------------------------------------------
            --}}
            <div class="ms-two-cols">
                {{-- KOMPOSISI INTERAKSI --}}
                <x-filament::section icon="heroicon-o-heart" icon-color="danger">
                    <x-slot name="heading">Komposisi Interaksi Konten</x-slot>
                    <x-slot name="description">Donat interaksi tersinkronisasi dengan total dari Meta API.</x-slot>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div
                            style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 24px; padding: 12px 0;">
                            <div
                                style="position: relative; width: 200px; height: 200px; border-radius: 9999px; box-shadow: 0 4px 14px rgba(0,0,0,0.08); background: conic-gradient({{ $donutGradientString }});">
                                <div style="position: absolute; inset: 0; margin: auto; width: 140px; height: 140px; border-radius: 9999px; background: #ffffff; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: inset 0 2px 6px rgba(0,0,0,0.06);"
                                    class="dark:bg-gray-900">
                                    <span
                                        style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em;">Total
                                        Interaksi</span>
                                    <span style="font-size: 26px; font-weight: 900; color: #0f172a; margin-top: 2px;"
                                        class="dark:text-white">{{ $formatNumber($donutTotal) }}</span>
                                </div>
                            </div>

                            <div style="width: 100%; display: flex; flex-direction: column; gap: 8px;">
                                @foreach ($interactionParts as $metricKey => $part)
                                    @php
                                        $partValue = (int) ($part['value'] ?? 0);
                                        $partPercent = $donutTotal > 0 ? round(($partValue / $donutTotal) * 100, 1) : 0;
                                    @endphp
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #f8fafc; border-radius: 10px; border: 1px solid #f1f5f9;"
                                        class="dark:bg-gray-800 dark:border-gray-700">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span
                                                style="width: 10px; height: 10px; border-radius: 9999px; background-color: {{ $part['color'] ?? '#cbd5e1' }}; flex-shrink: 0;"></span>
                                            <span style="font-size: 12.5px; font-weight: 600; color: #334155;"
                                                class="dark:text-gray-300">{{ $part['label'] ?? $metricKey }}</span>
                                        </div>
                                        <div>
                                            <span style="font-size: 13px; font-weight: 800; color: #0f172a;"
                                                class="dark:text-white">{{ $formatNumber($partValue) }}</span>
                                            <span
                                                style="font-size: 11px; font-weight: 600; color: #94a3b8; margin-left: 4px;">({{ $formatDecimal($partPercent) }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </x-filament::section>

                {{-- FUNNEL PROFIL --}}
                <x-filament::section icon="heroicon-o-funnel" icon-color="primary">
                    <x-slot name="heading">Funnel dari Jangkauan ke Follow</x-slot>
                    <x-slot name="description">Membaca alur konversi audiens dari melihat konten hingga mengikuti
                        profil @bpvppangkep.</x-slot>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach ($funnelSteps as $index => $step)
                            <div class="ms-card" style="padding: 14px 16px;">
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <span
                                            style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 9999px; background: #e0e7ff; color: #4338ca; font-size: 11px; font-weight: 800;">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <div style="font-size: 13.5px; font-weight: 800; color: #0f172a;"
                                                class="dark:text-white">{{ $step['label'] }}</div>
                                            <div style="font-size: 11.5px; color: #64748b;">{{ $step['rate_label'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div style="font-size: 20px; font-weight: 900; color: #0f172a;"
                                        class="dark:text-white">
                                        {{ $formatNumber($step['value']) }}
                                    </div>
                                </div>

                                <div class="ms-progress-bar" style="margin-top: 10px;">
                                    <span
                                        style="width: {{ max($step['value'] > 0 ? 3 : 0, min(100, $step['rate'])) }}%; background: {{ $index === 0 ? '#3b82f6' : ($index === 1 ? '#8b5cf6' : ($index === 2 ? '#f59e0b' : '#10b981')) }};"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            </div>

            {{--
            |--------------------------------------------------------------------------
            | 6. AKTIVITAS DARI PROFIL INSTAGRAM
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-cursor-arrow-rays" icon-color="primary">
                <x-slot name="heading">Aktivitas dari Profil Instagram</x-slot>
                <x-slot name="description">Ringkasan kunjungan profil, klik tautan website, dan tombol tindakan
                    kontak.</x-slot>

                <div class="ms-two-cols">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div class="ms-card" style="padding: 16px 20px;">
                            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">
                                Total Kunjungan Profil</div>
                            <div style="font-size: 26px; font-weight: 900; color: #0f172a; margin-top: 4px;"
                                class="dark:text-white">{{ $formatNumber($getMetric('profile_views')) }}</div>
                            <div style="font-size: 11.5px; color: #94a3b8; margin-top: 2px;">Jumlah audiens yang
                                membuka halaman profil Instagram BPVP Pangkep.</div>
                        </div>

                        <div class="ms-card" style="padding: 16px 20px;">
                            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">
                                Klik Tautan Website / Bio</div>
                            <div style="font-size: 26px; font-weight: 900; color: #0f172a; margin-top: 4px;"
                                class="dark:text-white">{{ $formatNumber($getMetric('website_clicks')) }}</div>
                            <div style="font-size: 11.5px; color: #94a3b8; margin-top: 2px;">Jumlah klik pada tautan
                                portal resmi atau shortlink di bio Instagram.</div>
                        </div>
                    </div>

                    <div>
                        <div
                            style="font-size: 11.5px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 10px; letter-spacing: 0.05em;">
                            Rincian Tombol Kontak Profil</div>
                        @if (!empty($profileActionsBreakdown))
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach ($profileActionsBreakdown as $type => $count)
                                    <div class="ms-card"
                                        style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px;">
                                        <span
                                            style="font-size: 12.5px; font-weight: 700; text-transform: capitalize; color: #334155;"
                                            class="dark:text-gray-300">{{ str_replace('_', ' ', $type) }}</span>
                                        <span style="font-size: 14px; font-weight: 900; color: #0f172a;"
                                            class="dark:text-white">{{ $formatNumber($count) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="ms-card"
                                style="border: 2px dashed #cbd5e1; padding: 24px; text-align: center; font-size: 12.5px; color: #94a3b8;">
                                Tidak ada aktivitas tombol kontak tercatat pada periode ini.
                            </div>
                        @endif
                    </div>
                </div>
            </x-filament::section>

            {{--
            |--------------------------------------------------------------------------
            | 7. PUNCAK FOLLOWER ONLINE (24 JAM)
            |--------------------------------------------------------------------------
            --}}
            @if (!empty($onlineFollowersHourly))
                <x-filament::section icon="heroicon-o-clock" icon-color="info">
                    <x-slot name="heading">Distribusi Follower Online (24 Jam)</x-slot>
                    <x-slot name="description">Waktu saat pengikut @bpvppangkep paling banyak aktif dalam
                        sehari.</x-slot>

                    <div style="display: flex; align-items: flex-end; gap: 4px; height: 110px; padding-top: 10px; overflow-x: auto;"
                        class="ms-scrollbar">
                        @php
                            $maxOnline = max(array_values($onlineFollowersHourly) ?: [1]);
                        @endphp
                        @foreach ($onlineFollowersHourly as $hour => $count)
                            @php
                                $heightPercent = $maxOnline > 0 ? round(($count / $maxOnline) * 100) : 0;
                                $isPeak = $count === $maxOnline && $count > 0;
                            @endphp
                            <div style="flex: 1; min-width: 22px; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;"
                                title="Pukul {{ sprintf('%02d:00', $hour) }}: {{ $formatNumber($count) }} follower online">
                                <div
                                    style="width: 100%; height: {{ max(4, $heightPercent) }}%; border-radius: 4px 4px 0 0; background: {{ $isPeak ? 'linear-gradient(to top, #ec4899, #f43f5e)' : '#60a5fa' }}; box-shadow: {{ $isPeak ? '0 2px 6px rgba(244,63,94,0.4)' : 'none' }}; transition: all 0.2s;">
                                </div>
                                <span
                                    style="font-size: 9.5px; font-weight: 700; color: #94a3b8; margin-top: 4px;">{{ sprintf('%02d', $hour) }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($onlineFollowersPeakHour !== null)
                        <div style="margin-top: 14px;">
                            <span class="ms-badge"
                                style="background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; padding: 6px 14px; font-size: 12px;">
                                <x-filament::icon icon="heroicon-o-fire" class="h-4 w-4" style="color: #db2777;" />
                                <span>Puncak aktivitas: Pukul {{ sprintf('%02d:00', $onlineFollowersPeakHour) }}
                                    WITA</span>
                            </span>
                        </div>
                    @endif
                </x-filament::section>
            @endif

            {{--
            |--------------------------------------------------------------------------
            | 8. DEMOGRAFI AUDIENS
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-user-group" icon-color="primary">
                <x-slot name="heading">Demografi Audiens Akun</x-slot>
                <x-slot name="description">Karakteristik umur, gender, dan lokasi pengikut serta akun
                    terjangkau.</x-slot>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        @foreach ($demographicMetrics as $demKey)
                            <button type="button" wire:click="loadDemographicMetric('{{ $demKey }}')"
                                class="ms-btn ms-btn-secondary" style="font-size: 12px; padding: 7px 14px;">
                                <x-filament::icon icon="heroicon-o-arrow-down-tray" class="h-4 w-4"
                                    style="color: #4f46e5;" />
                                <span>Muat {{ $metricDefinitions[$demKey]['label'] ?? $demKey }}</span>
                            </button>
                        @endforeach
                    </div>

                    @if ($demographicStatusMessage)
                        <div
                            style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 10px; padding: 10px 14px; font-size: 12px; color: #4338ca;">
                            {{ $demographicStatusMessage }}
                        </div>
                    @endif

                    @if (!empty($demographics))
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            @foreach ($demographics as $dKey => $data)
                                <div class="ms-card" style="padding: 18px;">
                                    <div style="font-size: 14px; font-weight: 800; color: #0f172a;"
                                        class="dark:text-white">{{ $metricDefinitions[$dKey]['label'] ?? $dKey }}
                                    </div>

                                    @foreach (['age', 'gender', 'city'] as $type)
                                        @if (!empty($data[$type]))
                                            <div style="margin-top: 14px;">
                                                <div
                                                    style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em;">
                                                    {{ ucfirst($type) }}</div>
                                                <div
                                                    style="display: flex; flex-direction: column; gap: 6px; margin-top: 6px;">
                                                    @foreach (array_slice($data[$type], 0, 5) as $row)
                                                        <div
                                                            style="display: flex; align-items: center; justify-content: space-between; font-size: 12px;">
                                                            <span style="color: #475569;"
                                                                class="dark:text-gray-300">{{ $row['label'] }}</span>
                                                            <span style="font-weight: 800; color: #0f172a;"
                                                                class="dark:text-white">{{ $formatNumber($row['value']) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-filament::section>

            {{--
            |--------------------------------------------------------------------------
            | 9. ANALISIS PERBANDINGAN PERIODE (TABEL KOMPARASI)
            |--------------------------------------------------------------------------
            --}}
            @if ($hasComparisonResult)
                <x-filament::section icon="heroicon-o-arrows-right-left" icon-color="primary">
                    <x-slot name="heading">Analisis Perbandingan Periode</x-slot>
                    <x-slot name="description">Membandingkan periode utama dengan periode pembanding secara
                        langsung.</x-slot>

                    <div class="ms-scrollbar" style="overflow-x: auto; width: 100%;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                            <thead>
                                <tr style="border-bottom: 2px solid #e2e8f0; font-size: 11px; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;"
                                    class="dark:border-gray-700 dark:text-gray-400">
                                    <th style="padding: 10px 14px;">Metric</th>
                                    <th style="padding: 10px 14px; text-align: right;">Periode Utama</th>
                                    <th style="padding: 10px 14px; text-align: right;">Pembanding</th>
                                    <th style="padding: 10px 14px; text-align: right;">Selisih</th>
                                    <th style="padding: 10px 14px; text-align: right;">Perubahan</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($comparisonMetricKeys as $metricKey)
                                    @php
                                        $definition = $metricDefinitions[$metricKey] ?? [];
                                        $current = $getMetric($metricKey);
                                        $previous = $getPrevMetric($metricKey);
                                        $currentStatus = $getMetricStatus($metricKey);
                                        $previousStatus = $getPrevMetricStatus($metricKey);
                                        $canCompare =
                                            $isAvailableStatus($currentStatus) && $isAvailableStatus($previousStatus);
                                        $difference = $canCompare ? $current - $previous : null;
                                        $percentage = $canCompare
                                            ? ($previous > 0
                                                ? round(($difference / $previous) * 100, 1)
                                                : ($current > 0
                                                    ? 100
                                                    : 0))
                                            : null;
                                        $positive = $canCompare && $difference >= 0;
                                    @endphp

                                    <tr style="border-bottom: 1px solid #f1f5f9;" class="dark:border-gray-800">
                                        <td style="padding: 10px 14px;">
                                            <div style="font-weight: 700; color: #0f172a;" class="dark:text-white">
                                                {{ $definition['label'] ?? $metricKey }}</div>
                                            <div style="font-size: 10px; text-transform: uppercase; color: #94a3b8;">
                                                {{ $metricKey }}</div>
                                        </td>

                                        <td style="padding: 10px 14px; text-align: right; font-weight: 800; color: #0f172a;"
                                            class="dark:text-white">
                                            {{ $displayMetricValue($metricKey, $current, $currentStatus) }}
                                        </td>
                                        <td style="padding: 10px 14px; text-align: right; color: #475569;"
                                            class="dark:text-gray-300">
                                            {{ $displayMetricValue($metricKey, $previous, $previousStatus) }}
                                        </td>

                                        <td
                                            style="padding: 10px 14px; text-align: right; font-weight: 700; color: {{ !$canCompare ? '#94a3b8' : ($positive ? '#16a34a' : '#dc2626') }};">
                                            @if ($canCompare)
                                                {{ $difference >= 0 ? '+' : '' }}{{ $formatNumber($difference) }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td
                                            style="padding: 10px 14px; text-align: right; font-weight: 700; color: {{ !$canCompare ? '#94a3b8' : ($positive ? '#16a34a' : '#dc2626') }};">
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
            @endif

            {{--
            |--------------------------------------------------------------------------
            | 10. PETUNJUK & CARA MEMBACA DASHBOARD
            |--------------------------------------------------------------------------
            --}}
            <x-filament::section icon="heroicon-o-information-circle" icon-color="gray">
                <x-slot name="heading">Petunjuk Penggunaan & Karakteristik Data</x-slot>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
                    <div class="ms-card" style="padding: 16px;">
                        <div style="font-weight: 800; font-size: 13.5px; color: #0f172a;" class="dark:text-white">
                            Total vs Komponen</div>
                        <div style="margin-top: 6px; font-size: 12px; line-height: 1.5; color: #64748b;"
                            class="dark:text-gray-300">
                            Total Interaksi Meta dapat berbeda dari jumlah komponen. Selisih ditampilkan secara
                            transparan pada grafik donat.
                        </div>
                    </div>
                    <div class="ms-card" style="padding: 16px;">
                        <div style="font-weight: 800; font-size: 13.5px; color: #0f172a;" class="dark:text-white">
                            Rasio Kinerja Turunan</div>
                        <div style="margin-top: 6px; font-size: 12px; line-height: 1.5; color: #64748b;"
                            class="dark:text-gray-300">
                            Rasio kinerja dihitung lokal dari data yang dimuat dan tidak memerlukan request API
                            tambahan.
                        </div>
                    </div>
                    <div class="ms-card" style="padding: 16px;">
                        <div style="font-weight: 800; font-size: 13.5px; color: #0f172a;" class="dark:text-white">
                            Filter Tidak Otomatis</div>
                        <div style="margin-top: 6px; font-size: 12px; line-height: 1.5; color: #64748b;"
                            class="dark:text-gray-300">
                            Perubahan tanggal baru diterapkan setelah tombol Ambil Data ditekan untuk menghemat batas
                            kuota API.
                        </div>
                    </div>
                    <div class="ms-card" style="padding: 16px;">
                        <div style="font-weight: 800; font-size: 13.5px; color: #0f172a;" class="dark:text-white">
                            Demografi Terpisah</div>
                        <div style="margin-top: 6px; font-size: 12px; line-height: 1.5; color: #64748b;"
                            class="dark:text-gray-300">
                            Data demografi dimuat manual sesuai kebutuhan per kategori agar pemuatan halaman tetap
                            cepat.
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- DEBUG --}}
        @if (!empty($this->debugData))
            <x-filament::section icon="heroicon-o-bug-ant" icon-color="danger">
                <x-slot name="heading">Debug Meta API</x-slot>

                <div class="ms-scrollbar"
                    style="max-height: 500px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px;">
                    @foreach ($this->debugData as $index => $debug)
                        <details class="ms-card" style="padding: 14px;">
                            <summary style="cursor: pointer;">
                                <span style="font-weight: 800; color: #0f172a;"
                                    class="dark:text-white">#{{ $index + 1 }} —
                                    {{ $debug['title'] ?? 'Debug' }}</span>
                                <span
                                    style="margin-left: 8px; font-size: 11.5px; color: #64748b;">{{ $debug['time'] ?? '' }}</span>
                            </summary>

                            <pre class="ms-scrollbar"
                                style="margin-top: 12px; overflow-x: auto; background: #020617; padding: 14px; border-radius: 10px; font-size: 11px; line-height: 1.6; color: #86efac;">{{ json_encode($debug['data'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                        </details>
                    @endforeach
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
