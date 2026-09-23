<!DOCTYPE html>
@extends('website::layouts.app')

@section('title', 'Struktur Organisasi - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Profil</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600">Struktur Organisasi</span>
            </nav>

            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">
                <div class="border-b border-slate-100 pb-5 flex flex-col sm:flex-row justify-between sm:items-end gap-4">
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Bagan
                            Birokrasi</span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">Struktur
                            Organisasi Balai</h1>
                    </div>
                    @if ($profil?->struktur_organisasi)
                        <a href="{{ asset('storage/' . $profil->struktur_organisasi) }}" target="_blank"
                            class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-xs shrink-0 text-center">
                            <i class="fas fa-download mr-1"></i> Unduh Bagan Resmi
                        </a>
                    @endif
                </div>

                <div class="flex justify-center bg-slate-50/50 rounded-2xl border border-slate-100 p-4">
                    @if ($profil?->struktur_organisasi)
                        <img src="{{ asset('storage/' . $profil->struktur_organisasi) }}"
                            alt="Bagan Struktur Organisasi BPVP Pangkep" class="max-w-full h-auto rounded-xl shadow-xs">
                    @else
                        <div class="text-center py-16 space-y-2 text-slate-400">
                            <div class="text-4xl text-slate-300"><i class="fas fa-sitemap"></i></div>
                            <p class="italic text-xs">File gambar bagan struktur organisasi belum diunggah.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection

