@extends('layouts.app')

@section('title', 'Fasilitas - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

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
                <span class="text-blue-600">Fasilitas & Kios</span>
            </nav>

            <div class="space-y-8">
                {{-- HEADER PAGE --}}
                <div class="border-b border-slate-200 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Sarana</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Gedung, Ruang Kelas &
                        Facilities</h1>
                    <p class="text-xs text-slate-500 mt-1">Prasarana penunjang kenyamanan ekosistem belajar mengajar di
                        Balai Pelatihan Vokasi.</p>
                </div>

                {{-- KONTAINER GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @forelse ($fasilitas ?? [] as $item)
                        @php
                            $item = (object) $item;
                        @endphp

                        {{-- WRAPPER KARTU & MODAL PER ITEM --}}
                        <div x-data="{ isOpen: false }" @keydown.escape.window="isOpen = false" class="h-full">

                            {{-- TAMPILAN KARTU UTAMA (BISA DIKLIK) --}}
                            <div @click="isOpen = true"
                                class="bg-white rounded-2xl overflow-hidden border border-slate-200/60 shadow-xs group flex flex-col justify-between h-full hover:shadow-md hover:border-blue-500/30 transition duration-300 cursor-pointer select-none">
                                <div>
                                    <div
                                        class="aspect-video bg-slate-100 overflow-hidden relative border-b border-slate-100">
                                        <div
                                            class="absolute inset-0 bg-slate-900/5 group-hover:bg-slate-900/20 transition duration-300 z-10 flex items-center justify-center">
                                            <span
                                                class="bg-white/90 backdrop-blur-xs text-xs font-bold text-slate-800 px-3 py-1.5 rounded-lg shadow-sm opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                                Lihat Sarana
                                            </span>
                                        </div>

                                        @if (!empty($item->foto_fasilitas))
                                            <img src="{{ asset('storage/' . $item->foto_fasilitas) }}"
                                                alt="{{ $item->nama_fasilitas }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        @else
                                            <div
                                                class="w-full h-full flex flex-col items-center justify-center text-slate-300 bg-slate-50 gap-2">
                                                <i class="fas fa-image text-3xl"></i>
                                                <span class="text-[10px] text-slate-400 font-medium">Tidak ada foto</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-5">
                                        <span
                                            class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block">Sarana
                                            BPVP</span>
                                        <h4
                                            class="font-extrabold text-slate-900 text-sm mt-1 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                                            {{ $item->nama_fasilitas }}
                                        </h4>

                                        <div class="text-xs text-slate-500 leading-relaxed line-clamp-3 font-normal">
                                            {{ strip_tags($item->deskripsi_fasilitas ?? 'Fasilitas penunjang praktik kerja terstandar.') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- PETUNJUK AKSES BAWAH KARTU --}}
                                <div
                                    class="px-5 pb-5 pt-2 flex items-center text-xs font-bold text-blue-600 gap-1.5 group-hover:gap-2.5 transition-all">
                                    <span>Rincian Fasilitas</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </div>
                            </div>

                            {{-- 🌟 POPUP MODAL COMPONENT (Hanya Muncul Saat Diklik) 🌟 --}}
                            <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto"
                                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

                                {{-- Backdrop Hitam Blur --}}
                                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="isOpen = false"></div>

                                {{-- Aligned Center Container --}}
                                <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">

                                    {{-- Kotak Inti Modal --}}
                                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl flex flex-col my-8"
                                        @click.away="isOpen = false" x-show="isOpen"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                                        {{-- TOMBOL SILANG POJOK KANAN ATAS --}}
                                        <button @click="isOpen = false"
                                            class="absolute right-4 top-4 z-10 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md text-slate-500 hover:text-slate-800 flex items-center justify-center shadow-xs border border-slate-200/50 transition cursor-pointer">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>

                                        {{-- HEADER BANNER MODAL (FOTO GEDUNG) --}}
                                        <div
                                            class="w-full h-64 sm:h-80 overflow-hidden bg-slate-50 border-b border-slate-100 relative">
                                            @if (!empty($item->foto_fasilitas))
                                                <img src="{{ asset('storage/' . $item->foto_fasilitas) }}"
                                                    alt="{{ $item->nama_fasilitas }}" class="w-full h-full object-cover">
                                            @else
                                                <div
                                                    class="w-full h-full flex flex-col items-center justify-center text-slate-300 bg-slate-100 gap-2">
                                                    <i class="fas fa-building text-5xl"></i>
                                                    <span class="text-xs text-slate-400 font-medium">Foto Sarana Belum
                                                        Diunggah</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- KONTEN TEKS LENGKAP --}}
                                        <div class="p-6 sm:p-8 space-y-4">
                                            <div>
                                                <span
                                                    class="text-[10px] font-bold text-blue-600 uppercase tracking-widest block mb-1">Prasarana
                                                    Balai Pelatihan</span>
                                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                                    {{ $item->nama_fasilitas }}
                                                </h2>
                                            </div>

                                            {{-- AREA RINGKASAN DESKRIPSI RICHEDITOR (Dengan Pembatas Scroll) --}}
                                            <div
                                                class="text-sm text-slate-600 leading-relaxed max-h-[35vh] overflow-y-auto pr-2 custom-scrollbar">
                                                <div class="prose prose-sm prose-slate max-w-none">
                                                    {!! $item->deskripsi_fasilitas ??
                                                        '<p class="italic text-slate-400">Belum ada keterangan deskripsi lengkap sarana.</p>' !!}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- TOMBOL PENUTUP BAWAH --}}
                                        <div
                                            class="bg-slate-50 px-6 py-4 flex justify-end rounded-b-2xl border-t border-slate-100">
                                            <button @click="isOpen = false"
                                                class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-5 text-xs rounded-xl transition cursor-pointer">
                                                Kembali
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
                                <i class="fas fa-building"></i>
                            </div>
                            <h4 class="font-bold text-slate-800 text-base">Belum Ada Data Fasilitas</h4>
                            <p class="text-xs text-slate-400 max-w-md mx-auto mt-1 leading-relaxed">
                                Informasi sarana prasarana, gedung workshop, maupun prasarana penunjang operasional balai
                                saat ini belum diinput oleh administrator.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection
