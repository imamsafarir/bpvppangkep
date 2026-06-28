@extends('layouts.app')

@section('title', $berita->judul_berita . ' - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@push('styles')
    <style>
        /* === PROTEKSI LAYOUT KONTEN BERITA === */
        .prose {
            max-width: 100%;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
        }

        .prose * {
            max-width: 100%;
            box-sizing: border-box;
            word-break: break-word;
        }

        /* === Gambar === */
        .prose img {
            max-width: 100%;
            height: auto;
            border-radius: 1rem;
            margin-left: auto;
            margin-right: auto;
        }

        /* === Paragraf === */
        .prose p {
            margin-bottom: 1.25rem;
            line-height: 1.75;
        }

        /* === List === */
        .prose ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .prose ol {
            list-style-type: decimal;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        /* === Tabel === */
        .prose table {
            display: block;
            width: 100%;
            overflow-x: auto;
            border-collapse: collapse;
        }

        /* === Code / Pre === */
        .prose pre,
        .prose code {
            white-space: pre-wrap;
            word-break: break-word;
            overflow-x: auto;
        }
    </style>
@endpush

@section('content')
    <main class="pt-32 pb-16 min-h-screen bg-slate-50/50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Breadcrumb Navigation --}}
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <a href="{{ route('berita.index') }}" class="hover:text-blue-600 transition-colors">Berita</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600 truncate max-w-[200px] sm:max-w-xs">{{ $berita->judul_berita }}</span>
            </nav>

            {{-- ARTIVEL UTAMA --}}
            <article class="bg-white rounded-3xl border border-slate-200/60 shadow-xs overflow-hidden">

                {{-- Image Banner Manager --}}
                @if ($berita->file_foto)
                    @php
                        $foto = $berita->file_foto;
                        if (is_string($foto)) {
                            $foto = json_decode($foto, true) ?? [$foto];
                        }
                        $gambarUtama = is_array($foto) ? $foto[0] ?? null : $foto;
                    @endphp

                    @if ($gambarUtama)
                        <div class="w-full aspect-video bg-slate-100 overflow-hidden">
                            <img src="{{ asset('storage/' . $gambarUtama) }}" alt="{{ $berita->judul_berita }}"
                                class="w-full h-full object-cover">
                        </div>
                    @endif
                @endif

                <div class="p-6 sm:p-10 space-y-6">
                    {{-- Metadata Berita --}}
                    <div class="border-b border-slate-100 pb-5 space-y-2">
                        <span
                            class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full uppercase tracking-wider inline-block">
                            Artikel Berita
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                            {{ $berita->judul_berita }}
                        </h1>
                        <div class="flex items-center gap-2 text-xs text-slate-400 pt-1 font-medium">
                            <i class="far fa-calendar-alt"></i>
                            <span>{{ $berita->created_at?->translatedFormat('d F Y H:i') ?? 'Baru saja' }} WITA</span>
                            <span class="text-slate-200">•</span>
                            <i class="far fa-user"></i>
                            <span>Administrator</span>
                        </div>
                    </div>

                    {{-- Isi Konten Utama --}}
                    <div class="text-slate-700 text-sm sm:text-base leading-relaxed max-w-none prose prose-slate">
                        {!! $berita->konten_berita !!}
                    </div>

                    {{-- 🟢 FITUR BARU: MEDIA SHARE BUTTONS (WhatsApp, FB, X, Copy Link) --}}
                    <div class="pt-6 border-t border-slate-100 space-y-3" x-data="{ copied: false }">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Bagikan Berita Ini</h4>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="https://api.whatsapp.com/send?text={{ rawurlencode($berita->judul_berita . ' - ' . url()->current()) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-xl font-bold text-xs transition duration-300">
                                <i class="fab fa-whatsapp text-sm"></i> WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-xl font-bold text-xs transition duration-300">
                                <i class="fab fa-facebook-f text-sm"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ rawurlencode(url()->current()) }}&text={{ rawurlencode($berita->judul_berita) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 hover:bg-slate-900 text-slate-800 hover:text-white rounded-xl font-bold text-xs transition duration-300">
                                <i class="fab fa-x-twitter text-sm"></i> Twitter
                            </a>
                            <button
                                @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2500)"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs transition duration-300 cursor-pointer"
                                :class="copied ? 'bg-green-500 text-white!' : 'hover:bg-slate-200'">
                                <i class="fas text-sm" :class="copied ? 'fa-check' : 'fa-link'"></i>
                                <span x-text="copied ? 'Tautan Tersalin!' : 'Salin Link'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- 🟢 FITUR BARU: NAVIGASI SEBELUMNYA & SELANJUTNYA (Kiri-Kanan) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-slate-100">
                        {{-- Halaman Sebelumnya --}}
                        <div>
                            @if (isset($prevBerita) && $prevBerita)
                                <a href="{{ route('berita.show', $prevBerita->id) }}"
                                    class="group block p-4 bg-slate-50 hover:bg-blue-50/50 border border-slate-200/50 rounded-2xl transition duration-300">
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1"><i
                                            class="fas fa-arrow-left"></i> Berita Sebelumnya</span>
                                    <span
                                        class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition-colors line-clamp-1">{{ $prevBerita->judul_berita }}</span>
                                </a>
                            @endif
                        </div>

                        {{-- Halaman Selanjutnya --}}
                        <div class="text-right">
                            @if (isset($nextBerita) && $nextBerita)
                                <a href="{{ route('berita.show', $nextBerita->id) }}"
                                    class="group block p-4 bg-slate-50 hover:bg-blue-50/50 border border-slate-200/50 rounded-2xl transition duration-300">
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Berita
                                        Selanjutnya <i class="fas fa-arrow-right"></i></span>
                                    <span
                                        class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition-colors line-clamp-1">{{ $nextBerita->judul_berita }}</span>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </article>

            {{-- 🟢 FITUR BARU: REKOMENDASI BERITA TERKAIT --}}
            @if (isset($beritaTerkait) && $beritaTerkait->count() > 0)
                <div class="space-y-4">
                    <h3 class="text-base font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <i class="fas fa-newspaper text-blue-600"></i> Baca Berita Lainnya
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach ($beritaTerkait as $terkait)
                            @php
                                $subFoto = $terkait->file_foto;
                                if (is_string($subFoto)) {
                                    $subFoto = json_decode($subFoto, true) ?? [$subFoto];
                                }
                                $gambarTerkait = is_array($subFoto) ? $subFoto[0] ?? null : $subFoto;
                            @endphp

                            <a href="{{ route('berita.show', $terkait->id) }}"
                                class="bg-white rounded-2xl border border-slate-200/50 shadow-xs overflow-hidden group hover:border-blue-500/30 hover:shadow-md transition duration-300 flex flex-col justify-between">
                                <div>
                                    <div class="w-full aspect-video bg-slate-100 overflow-hidden relative">
                                        @if ($gambarTerkait)
                                            <img src="{{ asset('storage/' . $gambarTerkait) }}"
                                                alt="{{ $terkait->judul_berita }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        @else
                                            <div
                                                class="w-full h-full flex flex-col items-center justify-center text-slate-300 bg-slate-50 gap-1 text-xs">
                                                <i class="fas fa-image text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h4
                                            class="font-bold text-slate-800 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors">
                                            {{ $terkait->judul_berita }}
                                        </h4>
                                    </div>
                                </div>
                                <div
                                    class="px-4 pb-4 pt-1 flex items-center justify-between text-[10px] font-medium text-slate-400">
                                    <span>{{ $terkait->created_at?->translatedFormat('d M Y') }}</span>
                                    <span
                                        class="text-blue-600 font-bold group-hover:translate-x-0.5 transition-transform"><i
                                            class="fas fa-chevron-right"></i></span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </main>
@endsection
