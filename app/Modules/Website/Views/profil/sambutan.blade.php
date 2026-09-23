@extends('website::layouts.app')

@section('title', 'Sambutan Kepala Balai - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@push('styles')
    <style>
        /* Style khusus halaman sambutan kepala untuk mengatur kejarakan spasi paragraf */
        .sambutan-content p,
        .sambutan-content div {
            display: block;
            margin-top: 0 !important;
            margin-bottom: 1.25rem !important;
        }

        .sambutan-content br {
            display: block;
            content: "";
            margin-top: 0.75rem;
        }
    </style>
@endpush

@section('content')
    <main class="pt-36 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumbs Navigation --}}
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Profil</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600">Sambutan Kepala</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- Komponen Kartu Foto Profil Kepala Balai --}}
                <div class="lg:col-span-4 lg:sticky lg:top-28 space-y-4 order-first lg:order-last">
                    <div
                        class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-5 text-center flex flex-col items-center">

                        <div
                            class="w-full aspect-[3/4] rounded-2xl overflow-hidden shadow-sm border-4 border-slate-50 bg-slate-100 mb-4">
                            @if (isset($profil->chief_photo_path) && $profil->chief_photo_path)
                                <img src="{{ asset('storage/' . $profil->chief_photo_path) }}" alt="Foto Kepala Balai"
                                    class="w-full h-full object-cover object-top">
                            @else
                                <div
                                    class="w-full h-full flex flex-col items-center justify-center text-xs text-slate-400 font-medium gap-2 py-20">
                                    <i class="fas fa-user-tie text-4xl text-slate-300"></i>
                                    <span>Foto belum diunggah</span>
                                </div>
                            @endif
                        </div>

                        <div class="leading-tight">
                            <h4 class="font-black text-slate-900 text-base tracking-tight">
                                {{ $profil?->chief_name ?? 'Nama Kepala Balai' }}
                            </h4>
                            <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mt-1.5">
                                Kepala BPVP Pangkep
                            </p>
                            @if (isset($profil->chief_nip) && $profil->chief_nip)
                                <div
                                    class="mt-3 pt-2.5 border-t border-slate-100 w-full font-mono text-[11px] text-slate-400">
                                    NIP. {{ $profil->chief_nip }}
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- Komponen Artikel Konten Teks Sambutan --}}
                <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 relative">
                    <div
                        class="text-slate-100 text-9xl font-serif absolute -top-10 -left-2 select-none pointer-events-none">
                        â€œ</div>

                    <div class="border-b border-slate-100 pb-4 relative z-10">
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Kata
                            Pengantar</span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">Sambutan
                            Kepala Balai</h1>
                    </div>

                    <div
                        class="text-slate-600 text-sm sm:text-base leading-relaxed relative z-10 pt-4 text-justify sambutan-content">
                        @if ($profil?->sambutan_kepala)
                            {!! $profil->sambutan_kepala !!}
                        @else
                            <p class="text-slate-400 italic text-center py-10">Konten lembar kata sambutan Kepala Balai
                                belum diisi.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

