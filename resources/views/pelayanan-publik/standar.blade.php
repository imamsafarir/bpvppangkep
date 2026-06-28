<!DOCTYPE html>
@extends('layouts.app')

@section('title', 'Standar Pelayanan - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-slate-500">Pelayanan</span>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-blue-600">Standar Pelayanan</span>
            </nav>

            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">
                <div class="border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Regulasi
                        Pelayanan</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Standar Pelayanan Publik
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Acuan standar baku maklumat pemberian layanan yang transparan
                        dan akuntabel kepada masyarakat.</p>
                </div>

                @php
                    $standarRaw = $pelayanan?->standar_pelayanan;

                    if (is_string($standarRaw)) {
                        $decoded = json_decode($standarRaw, true);
                        $standarList = is_array($decoded) ? $decoded : [];
                    } else {
                        $standarList = is_array($standarRaw) ? $standarRaw : [];
                    }
                @endphp

                <div class="space-y-8">
                    @forelse ($standarList as $item)
                        @php
                            $item = (object) $item;
                            $extension = !empty($item->file_standar)
                                ? pathinfo($item->file_standar, PATHINFO_EXTENSION)
                                : '';
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                        @endphp

                        <div
                            class="p-6 bg-slate-50 rounded-2xl border border-slate-200/50 space-y-6 hover:border-blue-500/20 transition-all">
                            {{-- Header --}}
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/60 pb-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">
                                        <i class="fas fa-gavel"></i>
                                    </div>
                                    <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                        {{ $item->judul_standar ?? 'Standar Pelayanan' }}</h3>
                                </div>

                                {{-- Download button jika PDF --}}
                                @if (!empty($item->file_standar) && !$isImage)
                                    <a href="{{ asset('storage/' . $item->file_standar) }}" target="_blank"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs shrink-0">
                                        <i class="fas fa-file-pdf"></i> Lihat / Unduh PDF
                                    </a>
                                @endif
                            </div>

                            {{-- Tampilan Gambar Utama secara langsung --}}
                            @if (!empty($item->file_standar) && $isImage)
                                <div
                                    class="w-full overflow-hidden rounded-xl border border-slate-200/80 bg-white p-2 shadow-xs">
                                    <img src="{{ asset('storage/' . $item->file_standar) }}"
                                        alt="{{ $item->judul_standar }}"
                                        class="w-full h-auto object-contain max-h-[800px] rounded-lg mx-auto">
                                </div>
                            @endif

                            {{-- Teks Deskripsi Tambahan --}}
                            @if (!empty($item->keterangan_standar))
                                <div
                                    class="bg-white p-5 rounded-xl border border-slate-200/40 text-xs sm:text-sm text-slate-600 leading-relaxed text-justify prose prose-slate max-w-none shadow-2xs">
                                    {!! $item->keterangan_standar !!}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-slate-50 text-slate-400 flex items-center justify-center text-2xl rounded-2xl mx-auto mb-4">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">Data Belum Tersedia</h3>
                            <p class="text-xs text-slate-400 mt-1">Dokumen standar pelayanan publik resmi belum diisi di
                                panel admin.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection
