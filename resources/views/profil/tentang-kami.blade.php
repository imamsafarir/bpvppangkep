@extends('layouts.app')

@section('title', 'Tentang Kami - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@push('styles')
    <style>
        .sambutan-content p,
        .sambutan-content div,
        .tentang-kami-content p,
        .tentang-kami-content div {
            display: block;
            margin-top: 0 !important;
            margin-bottom: 1.25rem !important;
            /* Jarak antar paragraf (enter 2x) */
        }

        .sambutan-content br,
        .tentang-kami-content br {
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
                <span class="text-blue-600">Tentang Kami</span>
            </nav>

            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">

                <div class="border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Profil
                        Balai</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                        Tentang Balai Pelatihan Vokasi dan Produktivitas Pangkep
                    </h1>
                </div>

                <div class="text-slate-600 text-sm sm:text-base leading-relaxed text-justify tentang-kami-content">
                    @if ($profil?->tentang_kami)
                        {!! $profil->tentang_kami !!}
                    @else
                        <div class="text-center py-12 space-y-3">
                            <div class="text-slate-300 text-5xl"><i class="fas fa-building-user"></i></div>
                            <p class="text-slate-400 italic">Informasi profil "Tentang Kami" belum diisi di panel admin.
                            </p>
                        </div>
                    @endif
                </div>

                <div
                    class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-semibold text-slate-400">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-halved text-blue-600"></i>
                        <span>Informasi Terverifikasi &bull; BPVP Pangkep</span>
                    </div>
                    <span class="font-mono text-[10px]">Pembaruan Terakhir:
                        {{ $profil?->updated_at ? date('d/m/Y', strtotime($profil->updated_at)) : '2026' }}</span>
                </div>

            </div>
        </div>
    </main>
@endsection
