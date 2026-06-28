@extends('layouts.app')

@section('title', 'Alumni - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BREADCRUMB --}}
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Informasi</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600">Alumni</span>
            </nav>

            <div class="space-y-8">
                {{-- HEADER PAGE --}}
                <div class="border-b border-slate-200 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Tracer Study</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Data Kebekerjaan Alumni Balai
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Laporan rekam jejak, prestasi, dan kontribusi sebaran karir
                        alumni lulusan BPVP Pangkep.</p>
                </div>

                @php
                    $jumlahAlumni = count($alumni ?? []);
                @endphp

                {{-- KONTAINER GRID UTAMA (Diisi Kendali Central Alpine.js untuk Kiri-Kanan) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ activeIndex: null, totalItems: {{ $jumlahAlumni }} }"
                    @keydown.escape.window="activeIndex = null">

                    @forelse ($alumni ?? [] as $index => $item)
                        @php
                            $item = (object) $item;
                        @endphp

                        {{-- 🟢 TAMPILAN KARTU LUAR (Bisa Diklik) --}}
                        <div @click="activeIndex = {{ $index }}"
                            class="bg-white rounded-2xl overflow-hidden border border-slate-200/60 shadow-xs group flex flex-col sm:flex-row hover:shadow-md hover:border-blue-500/30 transition duration-300 cursor-pointer select-none">

                            {{-- Foto Dokumentasi Alumni (Kiri) --}}
                            <div
                                class="sm:w-2/5 aspect-video sm:aspect-auto bg-slate-100 overflow-hidden relative min-h-[160px] flex-shrink-0">
                                @if (!empty($item->foto_kegiatan_alumni))
                                    <img src="{{ asset('storage/' . $item->foto_kegiatan_alumni) }}"
                                        alt="Kegiatan Alumni {{ $item->tahun_angkatan }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div
                                        class="w-full h-full flex flex-col items-center justify-center text-slate-300 bg-slate-50 gap-1 p-4 text-center">
                                        <i class="fas fa-user-graduate text-2xl text-slate-400"></i>
                                        <span class="text-[10px] text-slate-400">Dokumentasi Kosong</span>
                                    </div>
                                @endif
                                <div
                                    class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center z-10">
                                    <span
                                        class="bg-white/90 backdrop-blur-xs text-[10px] font-bold text-slate-800 px-2.5 py-1 rounded-md shadow-xs">Buka
                                        Detail</span>
                                </div>
                            </div>

                            {{-- Keterangan Singkat Alumni (Kanan) --}}
                            <div class="p-5 sm:w-3/5 flex flex-col justify-between overflow-hidden">
                                <div class="space-y-2">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 max-w-fit">
                                        <i class="fas fa-calendar-alt text-[9px]"></i> {{ $item->tahun_angkatan }}
                                    </span>

                                    {{-- Proteksi teks luar agar terpotong aman max 3 baris --}}
                                    <div
                                        class="text-xs text-slate-500 leading-relaxed line-clamp-3 font-normal break-words">
                                        {{ strip_tags($item->catatan_alumni ?? 'Informasi penyerapan kerja alumni.') }}
                                    </div>
                                </div>

                                <div
                                    class="pt-3 mt-3 border-t border-slate-50 flex items-center text-[11px] font-bold text-blue-600 gap-1 group-hover:gap-2 transition-all">
                                    <span>Lihat Laporan Rekam Jejak</span>
                                    <i class="fas fa-arrow-right text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- 🔵 POPUP MODAL TINGKAT LANJUT (Kiri-Kanan Sinkron) --}}
                        <div x-show="activeIndex === {{ $index }}" class="fixed inset-0 z-50 overflow-y-auto"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

                            {{-- Tirai Belakang Gelap --}}
                            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="activeIndex = null"></div>

                            {{-- Wadah Flex Tengah Layar + Navigasi Samping Screen --}}
                            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 relative">

                                {{-- ⬅️ TOMBOL NAVIGASI KIRI (PREVIOUS) --}}
                                <button @click.stop="activeIndex = (activeIndex === 0) ? totalItems - 1 : activeIndex - 1"
                                    class="fixed left-4 md:left-8 top-1/2 -translate-y-1/2 z-50 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/90 text-slate-700 hover:bg-blue-600 hover:text-white flex items-center justify-center shadow-lg border border-slate-200/50 transition cursor-pointer">
                                    <i class="fas fa-chevron-left text-sm md:text-base"></i>
                                </button>

                                {{-- KOTAK INTI ISI MODAL --}}
                                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl flex flex-col my-8 z-20 break-words"
                                    @click.away="activeIndex = null">

                                    {{-- Tombol Close X Silang Atas --}}
                                    <button @click="activeIndex = null"
                                        class="absolute right-4 top-4 z-30 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md text-slate-500 hover:text-slate-800 flex items-center justify-center shadow-xs border border-slate-200/50 transition cursor-pointer">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>

                                    {{-- Banner Foto Atas di Dalam Pop-up --}}
                                    <div
                                        class="w-full h-60 sm:h-72 overflow-hidden bg-slate-50 border-b border-slate-100 relative">
                                        @if (!empty($item->foto_kegiatan_alumni))
                                            <img src="{{ asset('storage/' . $item->foto_kegiatan_alumni) }}"
                                                alt="Dokumentasi Alumni" class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full flex flex-col items-center justify-center text-slate-300 bg-slate-100 gap-2">
                                                <i class="fas fa-user-graduate text-5xl"></i>
                                                <span class="text-xs text-slate-400 font-medium">Foto Dokumentasi Kegiatan
                                                    Belum Tersedia</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Blok Teks Data Detal Catatan --}}
                                    <div class="p-6 sm:p-8 space-y-4">
                                        <div>
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 mb-2">
                                                <i class="fas fa-calendar-alt text-xs"></i> Tahun Angkatan / Kelulusan:
                                                {{ $item->tahun_angkatan }}
                                            </span>
                                            <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                                                Laporan Sinergi Karir & Kinerja Alumni Balai
                                            </h2>
                                        </div>

                                        {{-- Area Scrollable dengan Proteksi Word-Wrap Penuh --}}
                                        <div
                                            class="text-sm text-slate-600 leading-relaxed max-h-[35vh] overflow-y-auto pr-2 custom-scrollbar break-words">
                                            <div
                                                class="prose prose-sm prose-slate max-w-none break-words whitespace-normal">
                                                {!! $item->catatan_alumni ?? '<p class="italic text-slate-400">Belum ada dokumen catatan tambahan.</p>' !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- FOOTER MODAL --}}
                                    <div
                                        class="bg-slate-50 px-6 py-4 flex justify-between items-center rounded-b-2xl border-t border-slate-100 select-none">
                                        <span class="text-[10px] sm:text-xs font-bold text-slate-400">
                                            Data {{ $index + 1 }} dari {{ $jumlahAlumni }} Alumni
                                        </span>
                                        <button @click="activeIndex = null"
                                            class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-5 text-xs rounded-xl transition cursor-pointer">
                                            Keluar
                                        </button>
                                    </div>

                                </div>

                                {{-- ➡️ TOMBOL NAVIGASI KANAN (NEXT) --}}
                                <button @click.stop="activeIndex = (activeIndex === totalItems - 1) ? 0 : activeIndex + 1"
                                    class="fixed right-4 md:right-8 top-1/2 -translate-y-1/2 z-50 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/90 text-slate-700 hover:bg-blue-600 hover:text-white flex items-center justify-center shadow-lg border border-slate-200/50 transition cursor-pointer">
                                    <i class="fas fa-chevron-right text-sm md:text-base"></i>
                                </button>

                            </div>
                        </div>

                    @empty
                        {{-- CADANGAN JIKA DATABASE KOSONG --}}
                        <div
                            class="col-span-1 md:col-span-2 bg-white rounded-3xl border border-slate-200/60 p-12 text-center shadow-xs">
                            <div
                                class="w-16 h-16 bg-slate-50 text-slate-400 flex items-center justify-center text-2xl rounded-2xl mx-auto mb-4 border border-slate-100">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Data Belum Tersedia</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto leading-relaxed">
                                Informasi database sebaran penyerapan kerja alumni belum dikonfigurasi melalui panel admin.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection
