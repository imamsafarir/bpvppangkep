<x-filament-panels::page>
    <style>
        .st-page-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            font-family: inherit;
        }

        .st-grid-two-cols {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 1024px) {
            .st-grid-two-cols {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .st-item-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .st-member-card {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .st-member-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.07);
            transform: translateY(-2px);
        }

        .dark .st-member-card {
            background: #18181b;
            border-color: #27272a;
        }

        .dark .st-member-card:hover {
            border-color: #3f3f46;
        }

        .st-member-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            width: 100%;
        }

        .st-member-ready {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .dark .st-member-ready {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .st-avatar {
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        }

        .st-rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 38px;
            padding: 0 8px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 13px;
            flex-shrink: 0;
            letter-spacing: -0.3px;
        }

        .st-rank-gold {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #b45309;
            border: 1px solid #fcd34d;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.15);
        }

        .st-rank-silver {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .st-rank-bronze {
            background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%);
            color: #c2410c;
            border: 1px solid #fdba74;
        }

        .st-rank-default {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .dark .st-rank-gold {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.4);
        }

        .dark .st-rank-silver {
            background: rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
            border-color: rgba(148, 163, 184, 0.3);
        }

        .dark .st-rank-bronze {
            background: rgba(249, 115, 22, 0.2);
            color: #fdba74;
            border-color: rgba(249, 115, 22, 0.4);
        }

        .dark .st-rank-default {
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .st-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
            line-height: 1.3;
            white-space: nowrap;
        }

        .st-badge-primary {
            background: #ede9fe;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .st-badge-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .st-badge-warning {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .st-badge-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .st-badge-info {
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .st-badge-gray {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .dark .st-badge-primary {
            background: rgba(147, 51, 234, 0.15);
            color: #c084fc;
            border-color: rgba(147, 51, 234, 0.3);
        }

        .dark .st-badge-success {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.3);
        }

        .dark .st-badge-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.3);
        }

        .dark .st-badge-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .dark .st-badge-info {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.3);
        }

        .dark .st-badge-gray {
            background: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
            border-color: rgba(255, 255, 255, 0.12);
        }

        .st-progress-bar-wrap {
            width: 100%;
            height: 4px;
            border-radius: 9999px;
            background: #f1f5f9;
            overflow: hidden;
            margin-top: 4px;
        }

        .dark .st-progress-bar-wrap {
            background: rgba(255, 255, 255, 0.08);
        }

        .st-progress-bar-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .st-count-number {
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.5px;
        }

        .st-count-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 3px;
        }
    </style>

    @php
        $summary = $this->getTeamSummary();

        $formatNumber = fn($value) => number_format((int) ($value ?? 0), 0, ',', '.');

        $topTotal = $summary['top_total'] ?? [];
        $topThisWeek = $summary['top_this_week'] ?? [];
        $noContributionThisWeek = $summary['no_contribution_this_week'] ?? [];
        $highestWorkload = $summary['highest_workload'] ?? [];

        $maxTotal = !empty($topTotal) ? max(1, (int) ($topTotal[0]['total'] ?? 1)) : 1;
        $maxWeek = !empty($topThisWeek) ? max(1, (int) ($topThisWeek[0]['total'] ?? 1)) : 1;
        $maxWorkload = !empty($highestWorkload) ? max(1, (int) ($highestWorkload[0]['beban'] ?? 1)) : 1;
    @endphp

    <div class="st-page-container">

        {{-- 1. SECTION LEADERBOARD PERFORMA TIM --}}
        <div class="st-grid-two-cols">

            {{-- KONTRIBUTOR ALL-TIME --}}
            <x-filament::section icon="heroicon-o-trophy" icon-color="success">
                <x-slot name="heading">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <span>Top Kontributor Tim (All-Time)</span>
                        <span class="st-badge st-badge-success" style="font-size: 10px;">🏆 Leaderboard</span>
                    </div>
                </x-slot>

                <x-slot name="description">
                    Peringkat anggota dengan kontribusi kumulatif terbanyak di tim media sosial.
                </x-slot>

                <div class="st-item-list">
                    @forelse ($topTotal as $index => $user)
                        @php
                            $rankClass = match ($index) {
                                0 => 'st-rank-gold',
                                1 => 'st-rank-silver',
                                2 => 'st-rank-bronze',
                                default => 'st-rank-default',
                            };
                            $rankLabel = match ($index) {
                                0 => '🥇 #1',
                                1 => '🥈 #2',
                                2 => '🥉 #3',
                                default => '#' . ($index + 1),
                            };
                            $pct = round((($user['total'] ?? 0) / $maxTotal) * 100);
                        @endphp
                        <div class="st-member-card">
                            <div class="st-member-row">
                                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                    <div class="st-rank-badge {{ $rankClass }}">
                                        {{ $rankLabel }}
                                    </div>

                                    <div class="st-avatar" style="background: {{ $user['avatar_bg'] ?? '#64748b' }};">
                                        {{ $user['initials'] ?? '?' }}
                                    </div>

                                    <div style="min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            <span style="font-weight: 700; font-size: 13.5px;"
                                                class="text-gray-900 dark:text-white">
                                                {{ $user['name'] ?? '-' }}
                                            </span>
                                            <span style="font-size: 11px; color: #94a3b8;">
                                                {{ $user['username'] ? '@' . $user['username'] : '' }}
                                            </span>
                                        </div>

                                        {{-- Role Badges --}}
                                        <div
                                            style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 3px;">
                                            @if (!empty($user['role_badges']))
                                                @foreach ($user['role_badges'] as $b)
                                                    <span class="st-badge st-badge-{{ $b['color'] ?? 'gray' }}">
                                                        <span>{{ $b['icon'] }}</span>
                                                        <span>{{ $b['label'] }}</span>
                                                    </span>
                                                @endforeach
                                            @else
                                                <span
                                                    style="font-size: 11px; color: #64748b;">{{ $user['roles'] ?? '-' }}</span>
                                            @endif
                                        </div>

                                        {{-- Metric Chips --}}
                                        <div
                                            style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 5px;">
                                            @if (($user['bahan'] ?? 0) > 0)
                                                <span class="st-badge st-badge-warning">📦
                                                    {{ $formatNumber($user['bahan']) }} Bahan</span>
                                            @endif
                                            @if (($user['final'] ?? 0) > 0)
                                                <span class="st-badge st-badge-info">✨
                                                    {{ $formatNumber($user['final']) }} Final</span>
                                            @endif
                                            @if (($user['editor'] ?? 0) > 0)
                                                <span class="st-badge st-badge-success">🎨
                                                    {{ $formatNumber($user['editor']) }} Edit</span>
                                            @endif
                                            @if (($user['admin'] ?? 0) > 0)
                                                <span class="st-badge st-badge-primary">🚀
                                                    {{ $formatNumber($user['admin']) }} Tayang</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div style="text-align: right; flex-shrink: 0;">
                                    <div class="st-count-number text-emerald-600 dark:text-emerald-400">
                                        {{ $formatNumber($user['total'] ?? 0) }}
                                    </div>
                                    <div class="st-count-label text-emerald-600/80 dark:text-emerald-400/80">
                                        Kontribusi
                                    </div>
                                </div>
                            </div>

                            {{-- Relative Progress Bar --}}
                            <div class="st-progress-bar-wrap" title="Kontribusi relatif: {{ $pct }}%">
                                <div class="st-progress-bar-fill"
                                    style="width: {{ $pct }}%; background: linear-gradient(90deg, #10b981, #059669);">
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-filament::empty-state icon="heroicon-o-trophy" heading="Belum Ada Kontribusi"
                            description="Belum ada riwayat kontribusi anggota tim yang tercatat." />
                    @endforelse
                </div>
            </x-filament::section>

            {{-- PALING AKTIF MINGGU INI --}}
            <x-filament::section icon="heroicon-o-fire" icon-color="warning">
                <x-slot name="heading">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <span>Paling Aktif Minggu Ini</span>
                        <span class="st-badge st-badge-warning" style="font-size: 10px;">🔥
                            {{ $summary['week_label'] ?? 'Minggu Ini' }}</span>
                    </div>
                </x-slot>

                <x-slot name="description">
                    Aktivitas dan kontribusi pada periode berjalan minggu ini.
                </x-slot>

                <div class="st-item-list">
                    @forelse ($topThisWeek as $index => $user)
                        @php
                            $pct = round((($user['total'] ?? 0) / $maxWeek) * 100);
                        @endphp
                        <div class="st-member-card">
                            <div class="st-member-row">
                                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                    <div class="st-rank-badge st-rank-gold">
                                        #{{ $index + 1 }}
                                    </div>

                                    <div class="st-avatar" style="background: {{ $user['avatar_bg'] ?? '#f59e0b' }};">
                                        {{ $user['initials'] ?? '?' }}
                                    </div>

                                    <div style="min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            <span style="font-weight: 700; font-size: 13.5px;"
                                                class="text-gray-900 dark:text-white">
                                                {{ $user['name'] ?? '-' }}
                                            </span>
                                            <span style="font-size: 11px; color: #94a3b8;">
                                                {{ $user['username'] ? '@' . $user['username'] : '' }}
                                            </span>
                                        </div>

                                        {{-- Role Badges --}}
                                        <div
                                            style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 3px;">
                                            @if (!empty($user['role_badges']))
                                                @foreach ($user['role_badges'] as $b)
                                                    <span class="st-badge st-badge-{{ $b['color'] ?? 'gray' }}">
                                                        <span>{{ $b['icon'] }}</span>
                                                        <span>{{ $b['label'] }}</span>
                                                    </span>
                                                @endforeach
                                            @else
                                                <span
                                                    style="font-size: 11px; color: #64748b;">{{ $user['roles'] ?? '-' }}</span>
                                            @endif
                                        </div>

                                        {{-- Metric Chips --}}
                                        <div
                                            style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 5px;">
                                            @if (($user['bahan'] ?? 0) > 0)
                                                <span class="st-badge st-badge-warning">📦
                                                    {{ $formatNumber($user['bahan']) }} Bahan</span>
                                            @endif
                                            @if (($user['final'] ?? 0) > 0)
                                                <span class="st-badge st-badge-info">✨
                                                    {{ $formatNumber($user['final']) }} Final</span>
                                            @endif
                                            @if (($user['editor'] ?? 0) > 0)
                                                <span class="st-badge st-badge-success">🎨
                                                    {{ $formatNumber($user['editor']) }} Edit</span>
                                            @endif
                                            @if (($user['admin'] ?? 0) > 0)
                                                <span class="st-badge st-badge-primary">🚀
                                                    {{ $formatNumber($user['admin']) }} Tayang</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div style="text-align: right; flex-shrink: 0;">
                                    <div class="st-count-number text-amber-600 dark:text-amber-400">
                                        {{ $formatNumber($user['total'] ?? 0) }}
                                    </div>
                                    <div class="st-count-label text-amber-600/80 dark:text-amber-400/80">
                                        Minggu Ini
                                    </div>
                                </div>
                            </div>

                            {{-- Relative Progress Bar --}}
                            <div class="st-progress-bar-wrap" title="Aktivitas minggu ini: {{ $pct }}%">
                                <div class="st-progress-bar-fill"
                                    style="width: {{ $pct }}%; background: linear-gradient(90deg, #f59e0b, #d97706);">
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-filament::empty-state icon="heroicon-o-fire" heading="Belum Ada Kontribusi Minggu Ini"
                            description="Belum ada riwayat aktivitas tim yang tercatat pada minggu ini." />
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        {{-- 2. SECTION BEBAN KERJA & EVALUASI TIM --}}
        <div class="st-grid-two-cols">

            {{-- ANGGOTA BELUM KONTRIBUSI MINGGU INI --}}
            <x-filament::section icon="heroicon-o-user-group" icon-color="info">
                <x-slot name="heading">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <span>Kesiapan Tugas Minggu Ini</span>
                        <span class="st-badge st-badge-info"
                            style="font-size: 10px;">{{ count($noContributionThisWeek) }} Anggota Siap</span>
                    </div>
                </x-slot>

                <x-slot name="description">
                    Anggota tim medsos yang belum memiliki tugas berjalan minggu ini dan siap menerima brief/konsep
                    baru.
                </x-slot>

                <div class="st-item-list">
                    @forelse ($noContributionThisWeek as $user)
                        <div class="st-member-card st-member-ready">
                            <div class="st-member-row">
                                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                    <div class="st-avatar" style="background: {{ $user['avatar_bg'] ?? '#64748b' }};">
                                        {{ $user['initials'] ?? '?' }}
                                    </div>

                                    <div style="min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            <span style="font-weight: 700; font-size: 13.5px;"
                                                class="text-gray-900 dark:text-white">
                                                {{ $user['name'] ?? '-' }}
                                            </span>
                                            <span style="font-size: 11px; color: #94a3b8;">
                                                {{ $user['username'] ? '@' . $user['username'] : '' }}
                                            </span>
                                        </div>

                                        <div
                                            style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 3px;">
                                            @if (!empty($user['role_badges']))
                                                @foreach ($user['role_badges'] as $b)
                                                    <span class="st-badge st-badge-{{ $b['color'] ?? 'gray' }}">
                                                        <span>{{ $b['icon'] }}</span>
                                                        <span>{{ $b['label'] }}</span>
                                                    </span>
                                                @endforeach
                                            @else
                                                <span
                                                    style="font-size: 11px; color: #64748b;">{{ $user['roles'] ?? '-' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div style="flex-shrink: 0;">
                                    <span class="st-badge st-badge-success"
                                        style="font-size: 11px; padding: 4px 10px;">
                                        🟢 Siap Ditugaskan
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-filament::empty-state icon="heroicon-o-check-badge" icon-color="success"
                            heading="Semua Anggota Sudah Aktif!"
                            description="Luar biasa! Seluruh anggota tim sosial media sudah aktif berkontribusi pada minggu ini." />
                    @endforelse
                </div>
            </x-filament::section>

            {{-- BEBAN TUGAS AKTIF TERTINGGI --}}
            <x-filament::section icon="heroicon-o-bolt" icon-color="primary">
                <x-slot name="heading">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <span>Beban Tugas Aktif Tertinggi</span>
                        <span class="st-badge st-badge-primary" style="font-size: 10px;">⚡ Antrean Aktif</span>
                    </div>
                </x-slot>

                <x-slot name="description">
                    Anggota yang saat ini memegang antrean tugas aktif paling banyak di alur kerja.
                </x-slot>

                <div class="st-item-list">
                    @forelse ($highestWorkload as $index => $user)
                        @php
                            $pct = round((($user['beban'] ?? 0) / $maxWorkload) * 100);
                            $bebanCount = (int) ($user['beban'] ?? 0);
                            $meterColor = match (true) {
                                $bebanCount <= 2 => '#10b981',
                                $bebanCount <= 4 => '#f59e0b',
                                default => '#ef4444',
                            };
                            $meterText = match (true) {
                                $bebanCount <= 2 => '🟢 Normal',
                                $bebanCount <= 4 => '🟡 Sibuk',
                                default => '🔴 Padat',
                            };
                        @endphp
                        <div class="st-member-card">
                            <div class="st-member-row">
                                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                    <div class="st-rank-badge st-rank-default">
                                        #{{ $index + 1 }}
                                    </div>

                                    <div class="st-avatar"
                                        style="background: {{ $user['avatar_bg'] ?? '#8b5cf6' }};">
                                        {{ $user['initials'] ?? '?' }}
                                    </div>

                                    <div style="min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            <span style="font-weight: 700; font-size: 13.5px;"
                                                class="text-gray-900 dark:text-white">
                                                {{ $user['name'] ?? '-' }}
                                            </span>
                                            <span class="st-badge st-badge-gray"
                                                style="font-size: 9.5px;">{{ $meterText }}</span>
                                        </div>

                                        <div
                                            style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 3px;">
                                            @if (!empty($user['role_badges']))
                                                @foreach ($user['role_badges'] as $b)
                                                    <span class="st-badge st-badge-{{ $b['color'] ?? 'gray' }}">
                                                        <span>{{ $b['icon'] }}</span>
                                                        <span>{{ $b['label'] }}</span>
                                                    </span>
                                                @endforeach
                                            @else
                                                <span
                                                    style="font-size: 11px; color: #64748b;">{{ $user['roles'] ?? '-' }}</span>
                                            @endif
                                        </div>

                                        {{-- Task details hint --}}
                                        <div
                                            style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 5px;">
                                            @if (($user['active'] ?? 0) > 0)
                                                <span class="st-badge st-badge-warning">📝 {{ $user['active'] }}
                                                    Konsep</span>
                                            @endif
                                            @if (($user['editor_revision'] ?? 0) > 0)
                                                <span class="st-badge st-badge-danger">🔄
                                                    {{ $user['editor_revision'] }} Revisi</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div style="text-align: right; flex-shrink: 0;">
                                    <div class="st-count-number text-purple-600 dark:text-purple-400">
                                        {{ $formatNumber($bebanCount) }}
                                    </div>
                                    <div class="st-count-label text-purple-600/80 dark:text-purple-400/80">
                                        Tugas Aktif
                                    </div>
                                </div>
                            </div>

                            {{-- Relative Progress Bar --}}
                            <div class="st-progress-bar-wrap" title="Beban tugas aktif: {{ $pct }}%">
                                <div class="st-progress-bar-fill"
                                    style="width: {{ $pct }}%; background: linear-gradient(90deg, #8b5cf6, {{ $meterColor }});">
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-filament::empty-state icon="heroicon-o-check-circle" icon-color="success"
                            heading="Antrean Bersih"
                            description="Tidak ada beban tugas aktif yang sedang menunggu antrean saat ini." />
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        {{-- 3. TABEL DETAIL PERFORMA SELURUH ANGGOTA --}}
        {{ $this->table }}

    </div>
</x-filament-panels::page>
