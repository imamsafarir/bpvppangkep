<!DOCTYPE html>
@extends('website::layouts.app')

@section('title', 'Visi Misi - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@push('styles')
    <style>
        /* 1. Perbaikan Enter Pargaraf Global */
        .sambutan-content p,
        .sambutan-content div,
        .tentang-kami-content p,
        .tentang-kami-content div,
        .ppid-content p,
        .ppid-content div,
        .visi-misi-content p,
        .visi-misi-content div {
            display: block;
            margin-top: 0 !important;
            margin-bottom: 1.25rem !important;
        }

        .sambutan-content br,
        .tentang-kami-content br,
        .ppid-content br,
        .visi-misi-content br {
            display: block;
            content: "";
            margin-top: 0.75rem;
        }

        /* 2. Spesifik Perbaikan Element RichEditor di Visi Misi */
        /* Membuat Heading 2 (H2) menjadi lebih besar dan tebal */
        .visi-misi-content h2 {
            display: block;
            font-size: 1.5rem !important;
            /* Ukuran teks lebih besar */
            font-weight: 800 !important;
            /* Super tebal font-black */
            color: #0f172a;
            /* Warna slate-900 */
            margin-top: 1.75rem !important;
            margin-bottom: 0.75rem !important;
            letter-spacing: -0.025em;
        }

        /* Menghidupkan kembali Ordered List (Daftar Angka: 1, 2, 3) */
        .visi-misi-content ol {
            display: block;
            list-style-type: decimal !important;
            padding-left: 1.5rem !important;
            margin-bottom: 1.25rem !important;
        }

        /* Menghidupkan kembali Unordered List (Daftar Bulatan / Bullet) */
        .visi-misi-content ul {
            display: block;
            list-style-type: disk !important;
            padding-left: 1.5rem !important;
            margin-bottom: 1.25rem !important;
        }

        /* Mengatur jarak antar item list agar rapi dan tidak berhimpitan */
        .visi-misi-content li {
            display: list-item;
            margin-bottom: 0.5rem !important;
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
                <span class="text-blue-600">Visi & Misi</span>
            </nav>

            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">
                <div class="border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Arah
                        Strategis</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">Visi & Misi
                        Instansi</h1>
                </div>

                <div class="text-slate-600 text-sm sm:text-base leading-relaxed text-justify visi-misi-content">
                    @if ($profil?->visi_misi)
                        {!! $profil->visi_misi !!}
                    @else
                        <div class="text-center py-12 text-slate-400 italic">Data Visi & Misi belum diisi.</div>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection

