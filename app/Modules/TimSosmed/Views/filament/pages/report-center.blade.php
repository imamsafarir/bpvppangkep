<x-filament-panels::page>
    {{-- SCOPED CSS AGAR TAMPILAN KONSISTEN & TIDAK TERPENGARUH CSS FILAMENT LAIN --}}
    <style>
        .rc-wrapper {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            box-sizing: border-box;
            font-family: inherit;
        }

        /* Hero Banner */
        .rc-hero-banner {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 24px;
            background: linear-gradient(135deg, #eef2ff 0%, #ffffff 50%, #ecfdf5 100%);
            border: 1px solid #c7d2fe;
            box-shadow: 0 4px 15px -3px rgba(79, 70, 229, 0.07);
        }

        .dark .rc-hero-banner {
            background: linear-gradient(135deg, #1e1b4b 0%, #18181b 50%, #064e3b 100%);
            border-color: #3730a3;
        }

        .rc-badge-gov {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 9999px;
            background: #e0e7ff;
            color: #3730a3;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #c7d2fe;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .dark .rc-badge-gov {
            background: rgba(99, 102, 241, 0.2);
            color: #c7d2fe;
            border-color: rgba(99, 102, 241, 0.4);
        }

        /* KPI Grid */
        .rc-kpi-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            width: 100%;
        }

        @media (min-width: 640px) {
            .rc-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .rc-kpi-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .rc-stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .rc-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.08);
        }

        .dark .rc-stat-card {
            background: #18181b;
            border-color: #27272a;
        }

        .rc-stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }

        .rc-card-indigo::before {
            background: #6366f1;
        }

        .rc-card-emerald::before {
            background: #10b981;
        }

        .rc-card-amber::before {
            background: #f59e0b;
        }

        .rc-card-sky::before {
            background: #0284c7;
        }

        .rc-stat-icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* 2-Column Analytics Grid */
        .rc-analytics-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .rc-analytics-grid {
                grid-template-columns: 1.6fr 1fr;
            }
        }

        .rc-panel-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 22px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .dark .rc-panel-card {
            background: #18181b;
            border-color: #27272a;
        }

        /* Progress Bar Platform */
        .rc-platform-row {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .rc-progress-track {
            height: 9px;
            width: 100%;
            background: #f1f5f9;
            border-radius: 9999px;
            overflow: hidden;
        }

        .dark .rc-progress-track {
            background: #27272a;
        }

        .rc-progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.5s ease-out;
        }

        /* Top Contributors Pill */
        .rc-team-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            transition: all 0.15s ease;
        }

        .dark .rc-team-pill {
            background: #1e293b;
            border-color: #334155;
        }

        .rc-team-pill:hover {
            border-color: #cbd5e1;
            transform: translateX(2px);
        }
    </style>

    <div class="rc-wrapper">

        {{-- 1. HERO BANNER RESMI BPVP PANGKEP --}}
        <div class="rc-hero-banner">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div style="max-width: 680px;">
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
                        <span class="rc-badge-gov">
                            🏛️ Kemnaker RI • BPVP Pangkep
                        </span>
                        <span style="font-size: 11.5px; font-weight: 600; color: #64748b;" class="dark:text-gray-400">
                            Pusat Pelaporan Media Sosial
                        </span>
                    </div>

                    <h1 style="font-size: 23px; font-weight: 900; color: #0f172a; letter-spacing: -0.4px; line-height: 1.25; margin: 0;"
                        class="dark:text-white">
                        Rekapitulasi & Pelaporan Resmi Publikasi Konten
                    </h1>
                    <p style="font-size: 13.5px; color: #475569; margin-top: 6px; line-height: 1.5;"
                        class="dark:text-gray-300">
                        Pantau produktivitas tim, rincian distribusi lintas platform, serta unduh dokumen laporan resmi
                        bertanda tangan digital untuk pertanggungjawaban program balai.
                    </p>
                </div>

                {{-- Status Periode Berjalan (Tanpa Tombol Ganda) --}}
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="background: rgba(255, 255, 255, 0.85); border: 1px solid #c7d2fe; padding: 10px 18px; border-radius: 14px; text-align: right;"
                        class="dark:bg-gray-800 dark:border-indigo-900">
                        <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;"
                            class="dark:text-gray-400">Periode Aktif</div>
                        <div style="font-size: 15px; font-weight: 900; color: #4338ca;" class="dark:text-indigo-400">
                            {{ now()->translatedFormat('F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. RINGKASAN KPI EKSEKUTIF (4 KARTU STATISTIK) --}}
        <div class="rc-kpi-grid">
            {{-- Kartu 1: Total Selesai --}}
            <div class="rc-stat-card rc-card-indigo">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <span
                        style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"
                        class="dark:text-gray-400">
                        Total Konten Selesai
                    </span>
                    <div class="rc-stat-icon-wrapper" style="background: #e0e7ff; color: #4338ca;">
                        <x-filament::icon icon="heroicon-o-check-badge" class="h-5 w-5" />
                    </div>
                </div>
                <div>
                    <div style="font-size: 28px; font-weight: 900; color: #0f172a; line-height: 1;"
                        class="dark:text-white">
                        {{ number_format($stats['totalSelesai'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 6px;" class="dark:text-gray-400">
                        <span style="color: #16a34a; font-weight: 700;">All-Time</span> publikasi berhasil tayang
                    </div>
                </div>
            </div>

            {{-- Kartu 2: Tayang Bulan Ini --}}
            <div class="rc-stat-card rc-card-emerald">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <span
                        style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"
                        class="dark:text-gray-400">
                        Publikasi Bulan Ini
                    </span>
                    <div class="rc-stat-icon-wrapper" style="background: #dcfce7; color: #15803d;">
                        <x-filament::icon icon="heroicon-o-calendar-days" class="h-5 w-5" />
                    </div>
                </div>
                <div>
                    <div style="font-size: 28px; font-weight: 900; color: #0f172a; line-height: 1;"
                        class="dark:text-white">
                        {{ number_format($stats['selesaiBulanIni'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 6px;" class="dark:text-gray-400">
                        Periode <span
                            style="font-weight: 700; color: #059669;">{{ now()->translatedFormat('F Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Kartu 3: Tayang Minggu Ini --}}
            <div class="rc-stat-card rc-card-amber">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <span
                        style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"
                        class="dark:text-gray-400">
                        Tayang Minggu Ini
                    </span>
                    <div class="rc-stat-icon-wrapper" style="background: #fef3c7; color: #b45309;">
                        <x-filament::icon icon="heroicon-o-sparkles" class="h-5 w-5" />
                    </div>
                </div>
                <div>
                    <div style="font-size: 28px; font-weight: 900; color: #0f172a; line-height: 1;"
                        class="dark:text-white">
                        {{ number_format($stats['selesaiMingguIni'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 6px;" class="dark:text-gray-400">
                        Aktivitas 7 hari terakhir
                    </div>
                </div>
            </div>

            {{-- Kartu 4: Platform Teraktif --}}
            <div class="rc-stat-card rc-card-sky">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <span
                        style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"
                        class="dark:text-gray-400">
                        Platform Teraktif
                    </span>
                    <div class="rc-stat-icon-wrapper" style="background: #e0f2fe; color: #0369a1;">
                        <x-filament::icon icon="heroicon-o-globe-alt" class="h-5 w-5" />
                    </div>
                </div>
                <div>
                    @if (!empty($stats['topPlatforms']))
                        @if (!empty($stats['topPlatformIsTie']))
                            <div
                                style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center; margin-top: 2px;">
                                @foreach ($stats['topPlatforms'] as $tp)
                                    <span
                                        style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 8px; font-size: 12.5px; font-weight: 800; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;"
                                        class="dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800">
                                        <span>{{ $tp['brand']['icon'] }}</span>
                                        <span>{{ $tp['name'] }}</span>
                                    </span>
                                @endforeach
                            </div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 6px;" class="dark:text-gray-400">
                                Masing-masing <strong style="color: #0284c7;">{{ $stats['topPlatformCount'] }}</strong>
                                konten (jumlah sama)
                            </div>
                        @else
                            @php $singleTop = $stats['topPlatforms'][0]; @endphp
                            <div style="display: flex; align-items: center; gap: 7px; font-size: 22px; font-weight: 900; color: #0f172a; line-height: 1.25;"
                                class="dark:text-white">
                                <span>{{ $singleTop['brand']['icon'] }}</span>
                                <span>{{ $singleTop['name'] }}</span>
                            </div>
                            <div style="font-size: 11.5px; color: #64748b; margin-top: 6px;" class="dark:text-gray-400">
                                Total <strong style="color: #0284c7;">{{ $stats['topPlatformCount'] }}</strong> konten
                                terpublikasi
                            </div>
                        @endif
                    @else
                        <div style="font-size: 18px; font-weight: 800; color: #94a3b8; line-height: 1.25;">
                            Belum Ada Data
                        </div>
                        <div style="font-size: 11.5px; color: #94a3b8; margin-top: 6px;">
                            Belum ada konten selesai
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 3. VISUAL ANALYTICS 2-KOLOM (GRAFIK TREN + DISTRIBUSI PLATFORM & TIM) --}}
        <div class="rc-analytics-grid">
            {{-- Sisi Kiri: Grafik Tren --}}
            <div class="rc-panel-card">
                <div
                    style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0;"
                            class="dark:text-white">
                            📈 Tren Produksi Konten Harian
                        </h3>
                        <p style="font-size: 12px; color: #64748b; margin-top: 2px; margin-bottom: 0;"
                            class="dark:text-gray-400">
                            Grafik jumlah konten berstatus selesai per tanggal posting bulan ini.
                        </p>
                    </div>
                    <span
                        style="font-size: 11.5px; font-weight: 700; padding: 3px 8px; border-radius: 8px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                        Live Update
                    </span>
                </div>

                {{-- Widget Grafik --}}
                <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%;">
                    <div style="min-width: 320px; width: 100%;">
                        @livewire(
                            \App\Modules\TimSosmed\Filament\Widgets\ContentChart::class,
                            [
                                'height' => '220px',
                                'showHeader' => false,
                            ],
                            key('report-center-chart-' . now()->timestamp)
                        )
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan: Distribusi Platform & Top Kontributor --}}
            <div class="rc-panel-card">
                <div>
                    <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0;" class="dark:text-white">
                        📊 Distribusi Lintas Platform
                    </h3>
                    <p style="font-size: 12px; color: #64748b; margin-top: 2px; margin-bottom: 0;"
                        class="dark:text-gray-400">
                        Persentase publikasi di kanal resmi BPVP Pangkep.
                    </p>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse ($stats['platformBreakdown'] ?? [] as $item)
                        <div class="rc-platform-row">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; font-size: 12px;">
                                <span
                                    style="font-weight: 700; color: #334155; display: flex; align-items: center; gap: 6px;"
                                    class="dark:text-gray-200">
                                    <span>{{ $item['brand']['icon'] }}</span>
                                    <span>{{ $item['name'] }}</span>
                                </span>
                                <span style="font-weight: 800; color: #0f172a;" class="dark:text-white">
                                    {{ $item['count'] }} Konten <span
                                        style="font-weight: 600; color: #64748b; font-size: 11px;">({{ $item['percentage'] }}%)</span>
                                </span>
                            </div>
                            <div class="rc-progress-track">
                                <div class="rc-progress-fill"
                                    style="width: {{ max($item['percentage'], 2) }}%; background: {{ $item['brand']['bar'] }};">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 20px 0; font-size: 12px; color: #94a3b8;">
                            Belum ada data publikasi platform.
                        </div>
                    @endforelse
                </div>

                {{-- Personil Tim Paling Aktif --}}
                <div style="border-top: 1px solid #e2e8f0; padding-top: 14px; margin-top: 4px;"
                    class="dark:border-gray-800">
                    <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;"
                        class="dark:text-gray-400">
                        Kontributor Teraktif Bulan Ini
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <div class="rc-team-pill">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 14px;">📋</span>
                                <div>
                                    <div
                                        style="font-size: 10px; font-weight: 800; color: #b45309; text-transform: uppercase;">
                                        Top Planner</div>
                                    <div style="font-size: 12px; font-weight: 700; color: #0f172a;"
                                        class="dark:text-white">{{ $stats['topPlanner'] }}</div>
                                </div>
                            </div>
                            <span
                                style="font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #fffbeb; color: #b45309; border: 1px solid #fde68a;">
                                {{ $stats['topPlannerCount'] }} draft
                            </span>
                        </div>

                        <div class="rc-team-pill">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 14px;">🎨</span>
                                <div>
                                    <div
                                        style="font-size: 10px; font-weight: 800; color: #065f46; text-transform: uppercase;">
                                        Top Editor</div>
                                    <div style="font-size: 12px; font-weight: 700; color: #0f172a;"
                                        class="dark:text-white">{{ $stats['topEditor'] }}</div>
                                </div>
                            </div>
                            <span
                                style="font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                                {{ $stats['topEditorCount'] }} edit
                            </span>
                        </div>

                        <div class="rc-team-pill">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 14px;">🚀</span>
                                <div>
                                    <div
                                        style="font-size: 10px; font-weight: 800; color: #1e40af; text-transform: uppercase;">
                                        Top Admin</div>
                                    <div style="font-size: 12px; font-weight: 700; color: #0f172a;"
                                        class="dark:text-white">{{ $stats['topAdmin'] }}</div>
                                </div>
                            </div>
                            <span
                                style="font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
                                {{ $stats['topAdminCount'] }} tayang
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. TABEL REKAPITULASI KONTEN RESMI --}}
        <div>
            <x-filament::section icon="heroicon-o-table-cells" collapsible>
                <x-slot name="heading">
                    <span style="font-size: 16px; font-weight: 800; color: #0f172a;" class="dark:text-white">
                        Daftar Konten Selesai Dipublikasikan
                    </span>
                </x-slot>
                <x-slot name="description">
                    Tabel rekapitulasi konten resmi BPVP Pangkep. Gunakan kotak centang di sisi kiri tabel untuk memilih
                    konten tertentu lalu klik "Download PDF Terpilih".
                </x-slot>

                <div style="margin-top: 12px;">
                    {{ $this->table }}
                </div>
            </x-filament::section>
        </div>

        {{-- 5. TIPS PENGGUNAAN & KETENTUAN LAPORAN --}}
        <div>
            <x-filament::callout icon="heroicon-o-shield-check" color="info">
                <x-slot name="heading">Petunjuk Pengesahan & Regulasi Pelaporan BPVP Pangkep</x-slot>
                <x-slot name="description">
                    <ul
                        style="margin: 4px 0 0 0; padding-left: 18px; font-size: 12px; line-height: 1.5; list-style-type: disc;">
                        <li>Dokumen PDF yang diunduh telah disesuaikan dengan format standar resmi Kementerian
                            Ketenagakerjaan RI lengkap dengan Kop Surat BPVP Pangkep.</li>
                        <li>Gunakan fitur <strong>"Download Laporan Lengkap"</strong> pada tombol atas untuk mengunduh
                            laporan periode triwulan, semester, atau tahunan dengan lembar pengesahan tanda tangan.</li>
                        <li>Tautan postingan (live link) pada tabel dan dokumen PDF dapat diklik secara langsung untuk
                            memverifikasi keabsahan konten di media sosial resmi (@bpvppangkep).</li>
                    </ul>
                </x-slot>
            </x-filament::callout>
        </div>

    </div>
</x-filament-panels::page>
