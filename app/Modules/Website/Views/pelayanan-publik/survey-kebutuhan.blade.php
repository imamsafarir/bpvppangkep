<!DOCTYPE html>
@extends('website::layouts.app')

@section('title', 'Survey Kebutuhan Pelatihan - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-slate-500">Pelayanan</span>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-blue-600">Survey Kebutuhan
                    Pelatihan</span>
            </nav>
            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">
                <div class="border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Perencanaan
                        Program</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Survey Kebutuhan Pelatihan
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Partisipasi Anda membantu kami merumuskan kejuruan program
                        diklat kerja yang relevan dengan kebutuhan industri bursa kerja saat ini.</p>
                </div>

                <div class="w-full rounded-2xl overflow-hidden border border-slate-200/80 bg-slate-50">
                    @if (!empty($pelayanan?->survey_kebutuhan_pelatihan))
                        <iframe src="{{ $pelayanan->survey_kebutuhan_pelatihan }}" width="100%" height="800"
                            frameborder="0" marginheight="0" marginwidth="0" class="w-full border-0">Memuatâ€¦</iframe>
                    @else
                        <p class="text-slate-400 italic text-center py-12">Tautan kuesioner survey analisis kebutuhan
                            pelatihan (AKP) belum tersedia.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection

