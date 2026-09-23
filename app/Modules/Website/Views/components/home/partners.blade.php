<section class="py-10 bg-white overflow-hidden border-y border-slate-100">

    {{-- JUDUL SECTION --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 text-center">
        <h3 class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-[0.2em]">
            Telah Dipercaya & Bekerja Sama Dengan
        </h3>
    </div>

    @php
        // Ambil data langsung dari Database
        $dataInformasi = \App\Modules\Website\Models\Informasi::first();
        $kerjasama = $dataInformasi?->kerjasama ?? [];
        $jumlahMitra = count($kerjasama);
    @endphp

    @if ($jumlahMitra > 0)

        @if ($jumlahMitra <= 5)
            {{-- ðŸŸ¢ KONDISI 1: JIKA DATA SEDIKIT (1-4 Logo) -> Tampil di Tengah & Diam --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-center gap-10 sm:gap-16">
                    @foreach ($kerjasama as $item)
                        <div
                            class="flex items-center gap-3 grayscale hover:grayscale-0 opacity-70 hover:opacity-100 transition-all duration-300 cursor-pointer select-none">

                            {{-- Render Logo --}}
                            @if (!empty($item['logo']))
                                <img src="{{ asset('storage/' . $item['logo']) }}"
                                    alt="{{ $item['nama_instansi'] ?? 'Mitra' }}"
                                    class="h-10 sm:h-12 w-auto max-w-[160px] object-contain flex-shrink-0" />
                            @else
                                <div
                                    class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-blue-600 shadow-inner border border-slate-200 flex-shrink-0">
                                    <i class="fas fa-building text-sm"></i>
                                </div>
                            @endif

                            {{-- Render Nama Instansi --}}
                            @if (!empty($item['nama_instansi']))
                                <span
                                    class="text-base sm:text-lg font-black text-slate-800 whitespace-nowrap tracking-tight">
                                    {{ $item['nama_instansi'] }}
                                </span>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- ðŸ”µ KONDISI 2: JIKA DATA BANYAK (5 Logo ke atas) -> Full Layar & Berjalan (Marquee) --}}
            @php
                // Trik Rahasia: Gandakan array 4x agar animasi tidak pernah terputus meskipun di layar super lebar
                $loopItems = array_merge($kerjasama, $kerjasama, $kerjasama, $kerjasama);
            @endphp

            <div class="relative w-full flex overflow-x-hidden group">

                {{-- Efek transparan memudar di kiri dan kanan --}}
                <div
                    class="absolute inset-y-0 left-0 w-40 sm:w-96 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none">
                </div>
                <div
                    class="absolute inset-y-0 right-0 w-40 sm:w-96 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none">
                </div>

                {{-- CSS Animasi Jalan Tanpa Henti --}}
                <style>
                    @keyframes scroll-x {
                        0% {
                            transform: translateX(0);
                        }

                        100% {
                            transform: translateX(-50%);
                        }

                        /* Translate -50% selalu pas karena array digandakan kelipatan genap (4x) */
                    }

                    .animate-scroll-x {
                        animation: scroll-x 40s linear infinite;
                        width: max-content;
                    }

                    /* Animasi berhenti jika disentuh mouse */
                    .group:hover .animate-scroll-x {
                        animation-play-state: paused;
                    }
                </style>

                {{-- Track / Jalur Berjalan --}}
                <div class="animate-scroll-x flex items-center gap-12 sm:gap-16 pl-12 sm:pl-16">
                    @foreach ($loopItems as $item)
                        <div
                            class="flex items-center gap-3 grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition-all duration-300 cursor-pointer select-none">

                            @if (!empty($item['logo']))
                                <img src="{{ asset('storage/' . $item['logo']) }}"
                                    alt="{{ $item['nama_instansi'] ?? 'Mitra' }}"
                                    class="h-10 sm:h-12 w-auto max-w-[140px] object-contain flex-shrink-0" />
                            @else
                                <div
                                    class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-blue-600 shadow-inner border border-slate-200 flex-shrink-0">
                                    <i class="fas fa-building text-sm"></i>
                                </div>
                            @endif

                            @if (!empty($item['nama_instansi']))
                                <span
                                    class="text-base sm:text-lg font-black text-slate-800 whitespace-nowrap tracking-tight">
                                    {{ $item['nama_instansi'] }}
                                </span>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        {{-- JIKA BELUM ADA DATA SAMA SEKALI --}}
        <div class="text-center text-xs text-slate-400 italic py-6">
            Belum ada data instansi kerja sama yang ditambahkan.
        </div>
    @endif

</section>

