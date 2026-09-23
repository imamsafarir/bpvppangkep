@extends('website::layouts.app')

@section('title', 'Galeri - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BREADCRUMB --}}
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-slate-500">Kabar Balai</span>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-blue-600">Galeri Kegiatan</span>
            </nav>

            <div class="space-y-8">
                {{-- HEADER PAGE --}}
                <div class="border-b border-slate-200 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Dokumentasi</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Galeri Foto Kegiatan</h1>
                    <p class="text-xs text-slate-500 mt-1">Dokumentasi visual serangkaian agenda, proses pelatihan, dan
                        momentum penting balai.</p>
                </div>

                {{-- KONTAINER GRID ALBUM --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-y-10 gap-x-6 pt-4">
                    @forelse ($galeri_list ?? [] as $galeri)
                        @php
                            $fotos = is_array($galeri->file_foto)
                                ? $galeri->file_foto
                                : json_decode($galeri->file_foto, true) ?? [];

                            $jumlahFoto = count($fotos);
                            $fotoSampul = $fotos[0] ?? null;
                        @endphp

                        {{-- KOMPONEN ALBUM DENGAN MANAGEMENT CAROUSEL ALPINE.JS --}}
                        <div x-data="{ isOpen: {{ request('id') == $galeri->id ? 'true' : 'false' }}, photoIndex: 0, totalPhotos: {{ $jumlahFoto }} }" @keydown.escape.window="isOpen = false"
                            class="relative group select-none">

                            {{-- ðŸ—‚ï¸ EFEK TUMPUKAN FOTO --}}
                            @if ($jumlahFoto > 1)
                                <div
                                    class="absolute inset-0 transform translate-x-2.5 -translate-y-2 bg-slate-300/60 border border-slate-400/20 rounded-2xl transition duration-300 group-hover:translate-x-4 group-hover:-translate-y-3.5 shadow-2xs">
                                </div>
                                <div
                                    class="absolute inset-0 transform translate-x-1.5 -translate-y-1 bg-slate-200 border border-slate-300/40 rounded-2xl transition duration-300 group-hover:translate-x-2 group-hover:-translate-y-2 shadow-xs">
                                </div>
                            @endif

                            {{-- KARTU ALBUM UTAMA --}}
                            <div @click="isOpen = true; photoIndex = 0"
                                class="relative bg-white rounded-2xl border border-slate-200/70 p-2.5 shadow-xs transition duration-300 group-hover:border-blue-500/30 group-hover:shadow-md cursor-pointer flex flex-col justify-between h-full z-10">
                                <div>
                                    <div
                                        class="aspect-square bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 relative overflow-hidden group/img">
                                        @if ($fotoSampul)
                                            <img src="{{ asset('storage/' . $fotoSampul) }}"
                                                alt="{{ $galeri->keterangan_galeri }}"
                                                class="w-full h-full object-cover group-hover:scale-102 transition duration-500">
                                        @else
                                            <div class="flex flex-col items-center gap-1">
                                                <i class="fas fa-image text-3xl text-slate-300"></i>
                                                <span class="text-[10px] text-slate-400">Kosong</span>
                                            </div>
                                        @endif

                                        <span
                                            class="absolute top-3 right-3 bg-slate-900/70 backdrop-blur-md text-white font-bold text-[10px] px-2 py-1 rounded-md tracking-wider flex items-center gap-1 shadow-xs">
                                            <i class="fas fa-images text-[9px]"></i> {{ $jumlahFoto }} FOTO
                                        </span>

                                        <div
                                            class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                            <span
                                                class="bg-white/95 backdrop-blur-xs text-slate-800 text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-xs">Buka
                                                Album</span>
                                        </div>
                                    </div>

                                    <div class="p-3 space-y-1">
                                        <h4 class="font-extrabold text-slate-800 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors duration-300"
                                            title="{{ $galeri->keterangan_galeri }}">
                                            {{ $galeri->keterangan_galeri }}
                                        </h4>
                                    </div>
                                </div>

                                <div
                                    class="px-3 pb-2 pt-1 border-t border-slate-50 flex items-center text-[10px] font-bold text-slate-400 justify-between">
                                    <span class="uppercase tracking-tight"><i class="far fa-calendar-alt"></i>
                                        {{ $galeri->created_at?->translatedFormat('d M Y') ?? '' }}</span>
                                    <span
                                        class="text-blue-600 flex items-center gap-0.5 group-hover:gap-1 transition-all">Lihat
                                        <i class="fas fa-chevron-right text-[8px]"></i></span>
                                </div>
                            </div>

                            {{-- ðŸ”µ INTERACTIVE POPUP LIGHTBOX MODAL (BRIGHT LIGHT MODE) ðŸŒŸ --}}
                            <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto"
                                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

                                {{-- Tirai Belakang Berubah Jadi Slate Netral Lembut --}}
                                <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-xs" @click="isOpen = false"></div>

                                {{-- Wrapper Frame --}}
                                <div class="flex min-h-full items-center justify-center p-4 md:p-10 relative">

                                    {{-- â¬…ï¸ PANAH KIRI (MODERN STYLING) --}}
                                    <button x-show="totalPhotos > 1"
                                        @click.stop="photoIndex = (photoIndex === 0) ? totalPhotos - 1 : photoIndex - 1"
                                        class="fixed left-4 md:left-8 top-1/2 -translate-y-1/2 z-50 w-12 h-12 md:w-14 md:h-14 rounded-full bg-white/90 hover:bg-blue-600 text-slate-700 hover:text-white flex items-center justify-center shadow-2xl border border-slate-200/50 transition duration-300 cursor-pointer group">
                                        <i
                                            class="fas fa-chevron-left text-sm md:text-base group-hover:-translate-x-0.5 transition-transform"></i>
                                    </button>

                                    {{-- ðŸ“¦ KOTAK UTAMA MODAL: Diubah dari max-w-3xl menjadi max-w-6xl (Jauh Lebih Besar) & bg-white --}}
                                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-6xl flex flex-col my-8 border border-slate-100"
                                        @click.away="isOpen = false" x-show="isOpen"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95">

                                        {{-- PANEL ATAS LIGHT MODE: Background Putih Bersih, Teks Slate Gelap --}}
                                        <div
                                            class="bg-slate-50/90 px-6 py-4 flex justify-between items-center border-b border-slate-200/60 sticky top-0 z-20 backdrop-blur-md">
                                            <div class="pr-8 overflow-hidden">
                                                <span
                                                    class="text-[10px] font-bold text-blue-600 uppercase tracking-widest block mb-0.5">Dokumentasi
                                                    Album Balai</span>
                                                <h2
                                                    class="text-sm md:text-base font-black text-slate-900 tracking-tight break-words line-clamp-1">
                                                    {{ $galeri->keterangan_galeri }}
                                                </h2>
                                            </div>
                                            <button @click="isOpen = false"
                                                class="w-8 h-8 rounded-full bg-white text-slate-400 hover:text-slate-700 flex items-center justify-center transition cursor-pointer flex-shrink-0 border border-slate-200 shadow-2xs">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>

                                        {{-- PANEL TENGAH: Tempat Kanvas Foto (Gunakan bg-slate-950 agar foto portrait/landscape beda ratio tetap aman dipandang tanpa distorsi) --}}
                                        <div
                                            class="w-full aspect-video bg-slate-950 flex items-center justify-center overflow-hidden relative group/nav min-h-[300px] sm:min-h-[450px] md:min-h-[550px]">
                                            @foreach ($fotos as $subIndex => $subFoto)
                                                <img src="{{ asset('storage/' . $subFoto) }}" alt="Dokumentasi Foto"
                                                    x-show="photoIndex === {{ $subIndex }}"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 scale-98"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    class="w-full h-full object-contain max-h-[75vh]">
                                            @endforeach
                                        </div>

                                        {{-- PANEL BAWAH LIGHT MODE: Teks Informatif Terang --}}
                                        <div
                                            class="bg-slate-50 px-6 py-4 border-t border-slate-200/60 space-y-4 text-slate-800">

                                            {{-- BARIS ATAS: Tanggal & Counter Foto --}}
                                            <div class="flex justify-between items-center">
                                                <span
                                                    class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">
                                                    Tanggal Agenda:
                                                    {{ $galeri->created_at?->translatedFormat('d F Y') ?? '' }}
                                                </span>
                                                <span
                                                    class="bg-blue-50 border border-blue-100 text-blue-600 px-3 py-1 rounded-md font-mono text-[10px] md:text-xs font-bold tracking-widest"
                                                    x-text="(photoIndex + 1) + ' / ' + totalPhotos">
                                                </span>
                                            </div>

                                            {{-- BARIS BAWAH: Tombol Salin Link & Download RAR/ZIP --}}
                                            {{-- x-data="{ copied: false }" dipasang di sini untuk mengontrol efek klik salin --}}
                                            <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-slate-100"
                                                x-data="{ copied: false }">

                                                {{-- ðŸ”— TOMBOL 1: Salin Link Langsung View Galeri (Sudah Diperbaiki) --}}
                                                <button x-data="{ copied: false }"
                                                    @click="
        const directLink = '{{ route('berita.galeri') }}?id={{ $galeri->id }}';
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(directLink);
        } else {
            let textArea = document.createElement('textarea');
            textArea.value = directLink;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
        copied = true;
        setTimeout(() => copied = false, 2500);
    "
                                                    class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-xl font-bold text-[11px] md:text-xs transition duration-300 cursor-pointer select-none"
                                                    :class="copied ? 'bg-green-500 text-white!' : ''">
                                                    <i class="fas" :class="copied ? 'fa-check' : 'fa-link'"></i>
                                                    <span
                                                        x-text="copied ? 'Tautan Tersalin!' : 'Salin Tautan Galeri'"></span>
                                                </button>

                                                {{-- ðŸ“¥ TOMBOL 2: Download RAR/ZIP Semua Foto Kegiatan --}}
                                                <a href="{{ route('galeri.download', $galeri->id) }}"
                                                    class="inline-flex items-center gap-2 px-3 py-2 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-xl font-bold text-[11px] md:text-xs transition duration-300 select-none">
                                                    <i class="fas fa-file-archive text-sm"></i> Unduh Semua Foto (ZIP)
                                                </a>

                                            </div>
                                        </div>

                                    </div>

                                    {{-- âž¡ï¸ PANAH KANAN (MODERN STYLING) --}}
                                    <button x-show="totalPhotos > 1"
                                        @click.stop="photoIndex = (photoIndex === totalPhotos - 1) ? 0 : photoIndex + 1"
                                        class="fixed right-4 md:right-8 top-1/2 -translate-y-1/2 z-50 w-12 h-12 md:w-14 md:h-14 rounded-full bg-white/90 hover:bg-blue-600 text-slate-700 hover:text-white flex items-center justify-center shadow-2xl border border-slate-200/50 transition duration-300 cursor-pointer group">
                                        <i
                                            class="fas fa-chevron-right text-sm md:text-base group-hover:translate-x-0.5 transition-transform"></i>
                                    </button>

                                </div>
                            </div>

                        </div>
                    @empty
                        {{-- JIKA DATA KOSONG --}}
                        <div
                            class="col-span-full bg-white rounded-3xl border border-slate-200/60 p-16 text-center shadow-2xs border-dashed">
                            <div
                                class="w-14 h-14 bg-slate-50 text-slate-400 flex items-center justify-center text-2xl rounded-2xl mx-auto mb-4 border border-slate-100">
                                <i class="fas fa-images"></i>
                            </div>
                            <h4 class="font-bold text-slate-800 text-sm">Belum Ada Galeri Foto</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 leading-relaxed">
                                Dokumentasi rangkuman potret kegiatan penunjang kompetensi kerja belum diunggah oleh pihak
                                admin balai.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection

