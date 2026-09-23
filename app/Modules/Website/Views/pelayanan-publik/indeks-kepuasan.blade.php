<!DOCTYPE html>
@extends('website::layouts.app')

@section('title', 'IKM - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-slate-500">Pelayanan</span>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-blue-600">Indeks Kepuasan
                    Masyarakat</span>
            </nav>
            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">
                <div class="border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Nilai
                        Akuntabilitas</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Indeks Kepuasan Masyarakat
                        (IKM)</h1>
                </div>
                <div class="text-slate-600 text-sm sm:text-base leading-relaxed prose prose-slate max-w-none">
                    @if ($pelayanan?->indeks_kepuasan_masyarakat)
                        {!! $pelayanan->indeks_kepuasan_masyarakat !!}
                    @else
                        <p class="text-slate-400 italic text-center py-12">Laporan infografis rekapitulasi nilai Indeks
                            Kepuasan Masyarakat (IKM) belum diisi.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection

