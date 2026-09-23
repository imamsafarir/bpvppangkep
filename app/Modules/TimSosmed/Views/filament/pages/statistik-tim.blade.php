<x-filament-panels::page>
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
        .ranking-scroll {
            max-height: 350px;
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-gutter: stable;
            padding-right: 6px;
        }

        .ranking-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .ranking-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .ranking-scroll::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.65);
            border-radius: 9999px;
        }

        .ranking-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 0.85);
        }

        .dark .ranking-scroll::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, 0.8);
        }
    </style>

    @php
        $summary = $this->getTeamSummary();

        $formatNumber = function ($value) {
            return number_format((int) ($value ?? 0), 0, ',', '.');
        };

        $topTotal = $summary['top_total'] ?? [];
        $topThisWeek = $summary['top_this_week'] ?? [];
        $noContributionThisWeek = $summary['no_contribution_this_week'] ?? [];
        $highestWorkload = $summary['highest_workload'] ?? [];
        $debug = $summary['debug'] ?? [];
    @endphp

    <div class="space-y-6">

        {{-- RINGKASAN ANGKA BESAR --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-filament::section icon="heroicon-o-users" icon-color="primary">
                <x-slot name="heading">
                    Total Anggota
                </x-slot>

                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $formatNumber($summary['total_members'] ?? 0) }}
                </div>

                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Anggota tim yang dihitung dalam statistik.
                </div>
            </x-filament::section>

            <x-filament::section icon="heroicon-o-trophy" icon-color="success">
                <x-slot name="heading">
                    Total Kontribusi
                </x-slot>

                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $formatNumber($summary['total_contribution_all'] ?? 0) }}
                </div>

                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Akumulasi seluruh kontribusi tim.
                </div>
            </x-filament::section>

            <x-filament::section icon="heroicon-o-calendar-days" icon-color="warning">
                <x-slot name="heading">
                    Kontribusi Minggu Ini
                </x-slot>

                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $formatNumber($summary['total_contribution_week'] ?? 0) }}
                </div>

                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ $summary['week_label'] ?? '-' }}
                </div>
            </x-filament::section>

            <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="danger">
                <x-slot name="heading">
                    Belum Kontribusi Minggu Ini
                </x-slot>

                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $formatNumber($summary['no_contribution_week_count'] ?? 0) }}
                </div>

                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Anggota dengan kontribusi 0 minggu ini.
                </div>
            </x-filament::section>
        </div>

        {{-- DEBUG RINGKAS
        <x-filament::section icon="heroicon-o-bug-ant" icon-color="warning">
            <x-slot name="heading">
                Debug Data Statistik Tim
            </x-slot>

            <x-slot name="description">
                Bagian ini membantu memastikan apakah data user dan content sudah terbaca oleh sistem.
                Kalau semua angka di sini 0, berarti sumber data di database memang belum terbaca oleh query.
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4">
                    <div class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Total User DB
                    </div>

                    <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $formatNumber($debug['total_users_db'] ?? 0) }}
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4">
                    <div class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Total Content DB
                    </div>

                    <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $formatNumber($debug['total_contents_db'] ?? 0) }}
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4">
                    <div class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        User Terbaca Statistik
                    </div>

                    <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $formatNumber($debug['users_terbaca_statistik'] ?? 0) }}
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4">
                    <div class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Content Minggu Ini
                    </div>

                    <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $formatNumber($debug['contents_minggu_ini'] ?? 0) }}
                    </div>
                </div>
            </div>

            <div
                class="mt-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800 dark:border-yellow-900 dark:bg-yellow-900/20 dark:text-yellow-200">
                <div class="font-bold mb-1">
                    Cara membaca debug:
                </div>

                <div>
                    Jika <strong>Total User DB</strong> ada nilainya, tetapi <strong>User Terbaca Statistik</strong> 0,
                    berarti query user masih tersaring. Jika <strong>Total Content DB</strong> ada nilainya tetapi
                    <strong>Total Kontribusi</strong> tetap 0, berarti kolom relasi content seperti
                    <code>pegawai_id</code>, <code>instruktur_id</code>, <code>planner_id</code>,
                    <code>editor_id</code>, atau <code>admin_id</code> belum sesuai dengan data.
                </div>
            </div>
        </x-filament::section> --}}

        {{-- PERTANYAAN STATISTIK TIM --}}
        <x-filament::section icon="heroicon-o-question-mark-circle" icon-color="primary">
            <x-slot name="heading">
                Pertanyaan yang Bisa Dijawab dari Statistik Tim
            </x-slot>

            <x-slot name="description">
                Ringkasan ini membantu melihat kontribusi, ranking, dan anggota yang perlu didorong aktivitasnya.
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 text-sm">
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4">
                    <div class="font-bold text-gray-900 dark:text-white">
                        Siapa paling banyak berkontribusi total?
                    </div>

                    <div class="mt-2 text-gray-500 dark:text-gray-400">
                        Dilihat dari total Konten Bahan, Konten Final, Selesai Edit, dan Tayang.
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4">
                    <div class="font-bold text-gray-900 dark:text-white">
                        Siapa paling aktif minggu ini?
                    </div>

                    <div class="mt-2 text-gray-500 dark:text-gray-400">
                        Menggunakan data kontribusi yang diperbarui pada minggu berjalan.
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4">
                    <div class="font-bold text-gray-900 dark:text-white">
                        Siapa belum pernah berkontribusi minggu ini?
                    </div>

                    <div class="mt-2 text-gray-500 dark:text-gray-400">
                        Menampilkan anggota dengan kontribusi 0 pada minggu ini.
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4">
                    <div class="font-bold text-gray-900 dark:text-white">
                        Siapa beban kerjanya paling tinggi?
                    </div>

                    <div class="mt-2 text-gray-500 dark:text-gray-400">
                        Dilihat dari konten aktif, antrean editor, revisi editor, dan antrean publish.
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- RANKING KONTRIBUSI --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <x-filament::section icon="heroicon-o-trophy" icon-color="success">
                <x-slot name="heading">
                    Ranking Kontribusi Total
                </x-slot>

                <x-slot name="description">
                    Siapa paling banyak berkontribusi secara keseluruhan?
                </x-slot>

                <div class="ranking-scroll space-y-3">
                    @forelse ($topTotal as $index => $user)
                        <div
                            class="flex items-start justify-between gap-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-green-700 font-bold dark:bg-green-900/30 dark:text-green-300">
                                    {{ $index + 1 }}
                                </div>

                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ $user['name'] ?? '-' }}
                                    </div>

                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $user['roles'] ?? '-' }}
                                    </div>

                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Bahan: {{ $formatNumber($user['bahan'] ?? 0) }},
                                        Final: {{ $formatNumber($user['final'] ?? 0) }},
                                        Edit: {{ $formatNumber($user['editor'] ?? 0) }},
                                        Tayang: {{ $formatNumber($user['admin'] ?? 0) }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $formatNumber($user['total'] ?? 0) }}
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    kontribusi
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4 text-sm text-gray-500 dark:text-gray-400 italic">
                            Belum ada data kontribusi total. Cek bagian Debug Data Statistik Tim di atas.
                        </div>
                    @endforelse
                </div>
            </x-filament::section>

            <x-filament::section icon="heroicon-o-calendar-days" icon-color="warning">
                <x-slot name="heading">
                    Ranking Kontribusi Minggu Ini
                </x-slot>

                <x-slot name="description">
                    Siapa paling aktif pada {{ $summary['week_label'] ?? 'minggu ini' }}?
                </x-slot>

                <div class="ranking-scroll space-y-3">
                    @forelse ($topThisWeek as $index => $user)
                        <div
                            class="flex items-start justify-between gap-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100 text-yellow-700 font-bold dark:bg-yellow-900/30 dark:text-yellow-300">
                                    {{ $index + 1 }}
                                </div>

                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ $user['name'] ?? '-' }}
                                    </div>

                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $user['roles'] ?? '-' }}
                                    </div>

                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Bahan: {{ $formatNumber($user['bahan'] ?? 0) }},
                                        Final: {{ $formatNumber($user['final'] ?? 0) }},
                                        Edit: {{ $formatNumber($user['editor'] ?? 0) }},
                                        Tayang: {{ $formatNumber($user['admin'] ?? 0) }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $formatNumber($user['total'] ?? 0) }}
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    minggu ini
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4 text-sm text-gray-500 dark:text-gray-400 italic">
                            Belum ada kontribusi minggu ini.
                        </div>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        {{-- BELUM KONTRIBUSI DAN BEBAN KERJA --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="danger">
                <x-slot name="heading">
                    Belum Berkontribusi Minggu Ini
                </x-slot>

                <x-slot name="description">
                    Siapa anggota yang belum punya kontribusi pada {{ $summary['week_label'] ?? 'minggu ini' }}?
                </x-slot>

                <div class="ranking-scroll space-y-3">
                    @forelse ($noContributionThisWeek as $user)
                        <div
                            class="flex items-center justify-between gap-4 rounded-lg border border-red-100 dark:border-red-900/50 bg-red-50 dark:bg-red-900/20 p-3">
                            <div>
                                <div class="font-semibold text-red-800 dark:text-red-300">
                                    {{ $user['name'] ?? '-' }}
                                </div>

                                <div class="text-xs text-red-700/70 dark:text-red-300/70">
                                    {{ $user['roles'] ?? '-' }}
                                </div>
                            </div>

                            <div class="text-xs font-bold text-red-700 dark:text-red-300">
                                0 kontribusi
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-lg border border-green-200 dark:border-green-900/50 bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-700 dark:text-green-300 italic">
                            Semua anggota sudah berkontribusi minggu ini, atau belum ada user yang terbaca pada
                            statistik.
                        </div>
                    @endforelse
                </div>
            </x-filament::section>

            <x-filament::section icon="heroicon-o-chart-bar" icon-color="danger">
                <x-slot name="heading">
                    Beban Kerja Aktif Tertinggi
                </x-slot>

                <x-slot name="description">
                    Siapa yang sedang memegang beban kerja aktif paling banyak?
                </x-slot>

                <div class="space-y-3">
                    @forelse ($highestWorkload as $index => $user)
                        <div
                            class="flex items-start justify-between gap-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white">
                                    #{{ $index + 1 }} — {{ $user['name'] ?? '-' }}
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $user['roles'] ?? '-' }}
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                    {{ $formatNumber($user['beban'] ?? 0) }}
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    beban aktif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-4 text-sm text-gray-500 dark:text-gray-400 italic">
                            Tidak ada beban kerja aktif.
                        </div>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        {{-- TABEL UTAMA --}}
        <x-filament::section icon="heroicon-o-table-cells" icon-color="gray">
            <x-slot name="heading">
                Detail Statistik Per Anggota
            </x-slot>

            <x-slot name="description">
                Tabel detail kontribusi dan beban kerja setiap anggota tim.
            </x-slot>

            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
