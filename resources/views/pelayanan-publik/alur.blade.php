<!DOCTYPE html>
@extends('layouts.app')

@section('title', 'Alur Pelayanan - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-slate-500">Pelayanan</span>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-blue-600">Alur Pelayanan</span>
            </nav>

            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">
                <div class="border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Prosedur</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Alur Mekanisme Pelayanan
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Diagram tahapan prosedur pelaksanaan pelayanan publik terpadu
                        di lingkungan balai.</p>
                </div>

                @php
                    $alurRaw = $pelayanan?->alur_pelayanan;

                    if (is_string($alurRaw)) {
                        $decoded = json_decode($alurRaw, true);
                        $alurList = is_array($decoded) ? $decoded : [];
                    } else {
                        $alurList = is_array($alurRaw) ? $alurRaw : [];
                    }
                @endphp

                <div class="space-y-10">
                    @forelse ($alurList as $item)
                        @php
                            $item = (object) $item;
                        @endphp

                        <div
                            class="p-6 bg-slate-50 rounded-2xl border border-slate-200/50 space-y-6 hover:border-blue-500/20 transition-all">
                            {{-- Judul Langkah Alur --}}
                            <div class="flex items-center gap-3 border-b border-slate-200/60 pb-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">
                                    <i class="fas fa-route"></i>
                                </div>
                                <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                    {{ $item->judul_alur ?? 'Langkah Pelayanan' }}</h3>
                            </div>

                            {{-- Render Bagan Gambar Secara Langsung --}}
                            @if (!empty($item->foto_alur))
                                <div
                                    class="w-full overflow-hidden rounded-xl border border-slate-200/80 bg-white p-2 shadow-xs">
                                    <img src="{{ asset('storage/' . $item->foto_alur) }}" alt="{{ $item->judul_alur }}"
                                        class="max-w-full h-auto object-contain max-h-[800px] rounded-lg mx-auto">
                                </div>
                            @endif

                            {{-- Keterangan / Deskripsi Teks Alur --}}
                            @if (!empty($item->deskripsi_alur))
                                <div
                                    class="bg-white p-5 rounded-xl border border-slate-200/40 text-xs sm:text-sm text-slate-600 leading-relaxed text-justify prose prose-slate max-w-none shadow-2xs">
                                    {!! $item->deskripsi_alur !!}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-slate-50 text-slate-400 flex items-center justify-center text-2xl rounded-2xl mx-auto mb-4">
                                <i class="fas fa-map-signs"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">Data Belum Tersedia</h3>
                            <p class="text-xs text-slate-400 mt-1">Bagan alur prosedur mekanisme pelayanan belum
                                dikonfigurasi di panel admin.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection
