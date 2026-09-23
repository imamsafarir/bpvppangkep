<!DOCTYPE html>
@extends('website::layouts.app')

@section('title', 'Tugas Fungsi - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@push('styles')
    <style>
        /* 1. Perbaikan Enter Paragraf Global */
        .sambutan-content p,
        .sambutan-content div,
        .tentang-kami-content p,
        .tentang-kami-content div,
        .ppid-content p,
        .ppid-content div,
        .visi-misi-content p,
        .visi-misi-content div,
        .tugas-fungsi-content p,
        .tugas-fungsi-content div {
            display: block;
            margin-top: 0 !important;
            margin-bottom: 1.25rem !important;
        }

        .sambutan-content br,
        .tentang-kami-content br,
        .ppid-content br,
        .visi-misi-content br,
        .tugas-fungsi-content br {
            display: block;
            content: "";
            margin-top: 0.75rem;
        }

        /* 2. Mengembalikan Format Heading & List dari RichEditor */
        .visi-misi-content h2,
        .tugas-fungsi-content h2 {
            display: block;
            font-size: 1.5rem !important;
            /* Ukuran teks judul besar */
            font-weight: 800 !important;
            /* Tebal font-black */
            color: #0f172a;
            /* Warna slate-900 */
            margin-top: 1.75rem !important;
            margin-bottom: 0.75rem !important;
            letter-spacing: -0.025em;
        }

        .visi-misi-content ol,
        .tugas-fungsi-content ol {
            display: block;
            list-style-type: decimal !important;
            /* Daftar Angka (1, 2, 3) */
            padding-left: 1.5rem !important;
            margin-bottom: 1.25rem !important;
        }

        .visi-misi-content ul,
        .tugas-fungsi-content ul {
            display: block;
            list-style-type: disc !important;
            /* Daftar Bullet Dot (â€¢) */
            padding-left: 1.5rem !important;
            margin-bottom: 1.25rem !important;
        }

        .visi-misi-content li,
        .tugas-fungsi-content li {
            display: list-item;
            margin-bottom: 0.5rem !important;
            /* Jarak renggang antar poin */
        }
    </style>
@endpush

@section('content')
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Profil</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600">Tugas & Fungsi</span>
            </nav>

            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">
                <div class="border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Tupoksi
                        Kerja</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">Tugas Pokok
                        & Fungsi Balai</h1>
                </div>

                <div class="text-slate-600 text-sm sm:text-base leading-relaxed text-justify tugas-fungsi-content">
                    @if ($profil?->tugas_fungsi)
                        {!! $profil->tugas_fungsi !!}
                    @else
                        <div class="text-center py-12 text-slate-400 italic">Data Tugas & Fungsi balai belum diisi.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection

