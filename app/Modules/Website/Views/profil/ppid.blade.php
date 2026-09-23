@extends('website::layouts.app')

@section('title', 'PPID - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@push('styles')
    <style>
        /* PERBAIKAN: Font-family dibungkus ke dalam selector utama agar tidak merusak CSS di bawahnya */
        .ppid-content {
            font-family: 'Poppins', sans-serif;
        }

        /* Perbaikan enter untuk Sambutan Kepala, Tentang Kami, dan PPID */
        .sambutan-content p,
        .sambutan-content div,
        .tentang-kami-content p,
        .tentang-kami-content div,
        .ppid-content p,
        .ppid-content div {
            display: block;
            margin-top: 0 !important;
            margin-bottom: 1.25rem !important;
            /* Jarak antar paragraf (enter 2x) */
        }

        .sambutan-content br,
        .tentang-kami-content br,
        .ppid-content br {
            display: block;
            content: "";
            margin-top: 0.75rem;
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
                <span class="text-blue-600">PPID</span>
            </nav>

            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">
                <div class="border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Layanan
                        Informasi</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                        Pejabat Pengelola Informasi dan Dokumentasi (PPID)
                    </h1>
                </div>

                <div class="text-slate-600 text-sm sm:text-base leading-relaxed text-justify ppid-content">
                    @if ($profil?->ppid)
                        {!! $profil->ppid !!}
                    @else
                        <div class="text-center py-12 text-slate-400 italic">
                            <i class="fas fa-circle-info text-3xl text-slate-300 block mb-2"></i>
                            Konten informasi layanan PPID belum diisi.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection

