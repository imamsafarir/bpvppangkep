@extends('layouts.app')

@section('title', 'Kejuruan - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

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
                <span class="text-blue-600">Kejuruan Pelatihan</span>
            </nav>

            <div class="space-y-6">
                {{-- HEADER PAGE --}}
                <div class="border-b border-slate-200 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Program
                        Pelatihan</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kejuruan Pelatihan Aktif</h1>
                    <p class="text-xs text-slate-500 mt-1">Daftar kejuruan program pelatihan kerja terstandar kompetensi di
                        BPVP Pangkep.</p>
                </div>

                {{-- KONTAINER GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($kejuruan ?? [] as $item)
                        @php
                            $item = (object) $item;
                        @endphp

                        {{-- PEMBUNGKUS KARTU + MODAL PER ITEM --}}
                        <div x-data="{ isOpen: false }" @keydown.escape.window="isOpen = false">

                            {{-- TAMPILAN KARTU UTAMA (BISA DIKLIK) --}}
                            <div @click="isOpen = true"
                                class="bg-white rounded-2xl border border-slate-200/60 shadow-xs overflow-hidden hover:shadow-md hover:border-blue-500/30 transition duration-300 group cursor-pointer h-full flex flex-col justify-between">
                                <div>
                                    @if (!empty($item->foto_kejuruan))
                                        <div
                                            class="w-full h-48 overflow-hidden bg-slate-100 border-b border-slate-100 relative">
                                            <img src="{{ asset('storage/' . $item->foto_kejuruan) }}"
                                                alt="{{ $item->nama_kejuruan }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                            {{-- Indikator hover kecil --}}
                                            <div
                                                class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                                <span
                                                    class="bg-white/90 backdrop-blur-xs text-xs font-bold text-slate-800 px-3 py-1.5 rounded-lg shadow-sm">Lihat
                                                    Detail</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="p-5">
                                        @if (empty($item->foto_kejuruan))
                                            <div
                                                class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                        @endif

                                        <h3
                                            class="font-extrabold text-slate-900 text-base mb-2 group-hover:text-blue-600 transition-colors duration-300">
                                            {{ $item->nama_kejuruan }}
                                        </h3>

                                        <div class="text-xs text-slate-500 mt-1 line-clamp-3 leading-relaxed font-normal">
                                            {{ strip_tags($item->deskripsi_kejuruan ?? 'Pelatihan berbasis kompetensi siap kerja.') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer Kartu Kecil --}}
                                <div
                                    class="px-5 pb-5 pt-2 flex items-center text-xs font-bold text-blue-600 gap-1.5 group-hover:gap-2.5 transition-all">
                                    <span>Selengkapnya</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </div>
                            </div>

                            {{-- 🌟 POPUP MODAL SCREEN (Hanya muncul jika kartu diklik) 🌟 --}}
                            <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto"
                                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

                                {{-- Backdrop Bayangan Hitam Transparan --}}
                                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="isOpen = false"></div>

                                {{-- Posisi Tengah Modal --}}
                                <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">

                                    {{-- Kotak Utama Modal --}}
                                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl flex flex-col my-8"
                                        @click.away="isOpen = false" x-show="isOpen"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                                        {{-- TOMBOL CLOSE POJOK KANAN --}}
                                        <button @click="isOpen = false"
                                            class="absolute right-4 top-4 z-10 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md text-slate-500 hover:text-slate-800 flex items-center justify-center shadow-xs border border-slate-200/50 transition cursor-pointer">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>

                                        {{-- JIKA ADA FOTO: Tampilkan Banner Besar di Atas Modal --}}
                                        @if (!empty($item->foto_kejuruan))
                                            <div
                                                class="w-full h-64 sm:h-80 overflow-hidden bg-slate-50 border-b border-slate-100">
                                                <img src="{{ asset('storage/' . $item->foto_kejuruan) }}"
                                                    alt="{{ $item->nama_kejuruan }}" class="w-full h-full object-cover">
                                            </div>
                                        @endif

                                        {{-- BADGE ICON JIKA TIDAK ADA FOTO --}}
                                        <div class="p-6 sm:p-8 space-y-4">
                                            @if (empty($item->foto_kejuruan))
                                                <div
                                                    class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                                                    <i class="fas fa-graduation-cap"></i>
                                                </div>
                                            @endif

                                            <div>
                                                <span
                                                    class="text-[10px] font-bold text-blue-600 uppercase tracking-widest block mb-1">Detail
                                                    Program Pelatihan</span>
                                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                                    {{ $item->nama_kejuruan }}
                                                </h2>
                                            </div>

                                            {{-- AREA ISI DESKRIPSI (Mendukung scroll jika teks sangat panjang) --}}
                                            <div
                                                class="text-sm text-slate-600 leading-relaxed max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                                                <div class="prose prose-sm prose-slate max-w-none">
                                                    {!! $item->deskripsi_kejuruan ??
                                                        '<p class="italic text-slate-400">Belum ada rincian deskripsi resmi untuk kejuruan ini.</p>' !!}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- FOOTER MODAL --}}
                                        <div
                                            class="bg-slate-50 px-6 py-4 flex justify-end rounded-b-2xl border-t border-slate-100">
                                            <button @click="isOpen = false"
                                                class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-5 text-xs rounded-xl transition cursor-pointer">
                                                Tutup Detail
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    @empty
                        {{-- JIKA DATA KOSONG --}}
                        <div
                            class="col-span-full text-center py-16 bg-white rounded-3xl border border-dashed border-slate-200 p-8 shadow-xs">
                            <div
                                class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-4 border border-slate-100">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h4 class="font-bold text-slate-800 text-base">Belum Ada Program Kejuruan</h4>
                            <p class="text-xs text-slate-400 max-w-md mx-auto mt-1 leading-relaxed">
                                Daftar kejuruan program pelatihan reguler aktif saat ini belum diinput atau sedang dalam
                                proses pembaruan oleh admin balai.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection
