@extends('website::layouts.app')

@section('title', 'Testimoni - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BREADCRUMB --}}
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Informasi</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600">Testimoni</span>
            </nav>

            <div class="space-y-8">
                {{-- HEADER PAGE --}}
                <div class="border-b border-slate-200 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Ulasan Peserta</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Apa Kata Mereka Tentang BPVP
                        Pangkep?</h1>
                    <p class="text-xs text-slate-500 mt-1">Ulasan jujur dan kisah sukses langsung dari alumni setelah
                        mengikuti program pelatihan vokasi.</p>
                </div>

                @php
                    $jumlahTestimoni = count($testimoni ?? []);
                @endphp

                {{-- KONTAINER GRID UTAMA (Dikendalikan Mandiri oleh Centralized Alpine.js) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-data="{ activeIndex: null, totalItems: {{ $jumlahTestimoni }} }"
                    @keydown.escape.window="activeIndex = null">

                    @forelse ($testimoni ?? [] as $index => $item)
                        @php
                            $item = (object) $item;
                        @endphp

                        {{-- ðŸŸ¢ TAMPILAN KARTU LUAR (BISA DIKLIK) --}}
                        <div @click="activeIndex = {{ $index }}"
                            class="bg-white rounded-2xl border border-slate-200/60 shadow-xs p-6 flex flex-col justify-between relative group hover:shadow-md hover:border-blue-500/30 transition duration-300 cursor-pointer select-none h-full">

                            {{-- Tanda Petik Dekorasi (Quote Icon) --}}
                            <div
                                class="absolute top-6 right-6 text-slate-100 text-5xl font-serif pointer-events-none select-none group-hover:text-blue-50/70 transition-colors duration-300">
                                â€œ
                            </div>

                            <div class="space-y-4">
                                {{-- Pratinjau Kalimat Utama Testimoni (Dipotong Maksimal 4 Baris Aman) --}}
                                <div
                                    class="text-xs text-slate-600 leading-relaxed italic font-normal line-clamp-4 break-words">
                                    "{{ strip_tags($item->isi_testimoni ?? 'Tidak ada ulasan tertulis.') }}"
                                </div>
                            </div>

                            {{-- Info Profil Alumni di Bagian Bawah Kartu --}}
                            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-slate-100 overflow-hidden">
                                <div
                                    class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                    @if (!empty($item->foto_alumni))
                                        <img src="{{ asset('storage/' . $item->foto_alumni) }}"
                                            alt="{{ $item->nama_alumni }}" class="w-full h-full object-cover">
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-500 text-sm font-bold">
                                            {{ strtoupper(substr($item->nama_alumni ?? 'A', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="overflow-hidden">
                                    <h5
                                        class="font-extrabold text-slate-900 text-xs truncate group-hover:text-blue-600 transition-colors">
                                        {{ $item->nama_alumni }}</h5>
                                    <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ $item->pekerjaan }}</p>
                                </div>
                            </div>

                        </div>

                        {{-- ðŸ”µ POPUP MODAL TINGKAT LANJUT (NAVIGASI SINKRON KIRI-KANAN) --}}
                        <div x-show="activeIndex === {{ $index }}" class="fixed inset-0 z-50 overflow-y-auto"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

                            {{-- Backdrop Hitam Blur Belakang --}}
                            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="activeIndex = null"></div>

                            {{-- Flex Center Posisi Modal + Tombol Slider Samping --}}
                            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 relative">

                                {{-- â¬…ï¸ TOMBOL NAVIGASI KIRI (PREVIOUS) --}}
                                <button @click.stop="activeIndex = (activeIndex === 0) ? totalItems - 1 : activeIndex - 1"
                                    class="fixed left-4 md:left-8 top-1/2 -translate-y-1/2 z-50 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/90 text-slate-700 hover:bg-blue-600 hover:text-white flex items-center justify-center shadow-lg border border-slate-200/50 transition cursor-pointer">
                                    <i class="fas fa-chevron-left text-sm md:text-base"></i>
                                </button>

                                {{-- KOTAK UTAMA MODAL TESTIMONI --}}
                                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-xl flex flex-col my-8 z-20 break-words"
                                    @click.away="activeIndex = null">

                                    {{-- Tombol Silang Pojok Kanan Atas --}}
                                    <button @click="activeIndex = null"
                                        class="absolute right-4 top-4 z-30 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md text-slate-500 hover:text-slate-800 flex items-center justify-center shadow-xs border border-slate-200/50 transition cursor-pointer">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>

                                    {{-- Isi Konten Ulasan Di Dalam Pop-up --}}
                                    <div class="p-6 sm:p-8 space-y-6 pt-10">

                                        {{-- Tanda Petik Dekorasi Pembuka Besar --}}
                                        <div
                                            class="text-blue-500/20 text-6xl font-serif h-4 -mb-4 select-none pointer-events-none">
                                            â€œ</div>

                                        {{-- Blok Teks Kalimat Ulasan Lengkap RichEditor (Word Wrap Aman) --}}
                                        <div
                                            class="text-sm sm:text-base text-slate-700 leading-relaxed italic max-h-[35vh] overflow-y-auto pr-2 custom-scrollbar break-words">
                                            <div
                                                class="prose prose-sm prose-slate max-w-none break-words whitespace-normal">
                                                {!! $item->isi_testimoni ?? '<p class="italic text-slate-400">Belum ada teks ulasan resmi.</p>' !!}
                                            </div>
                                        </div>

                                        {{-- Detal Informasi User Profil Pembuat Ulasan --}}
                                        <div class="flex items-center gap-4 pt-5 border-t border-slate-100">
                                            <div
                                                class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                                @if (!empty($item->foto_alumni))
                                                    <img src="{{ asset('storage/' . $item->foto_alumni) }}"
                                                        alt="{{ $item->nama_alumni }}" class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-500 text-base font-bold">
                                                        {{ strtoupper(substr($item->nama_alumni ?? 'A', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="overflow-hidden">
                                                <h4 class="font-black text-slate-900 text-sm tracking-tight">
                                                    {{ $item->nama_alumni }}</h4>
                                                <p class="text-xs text-slate-400 mt-0.5">{{ $item->pekerjaan }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- FOOTER MODAL --}}
                                    <div
                                        class="bg-slate-50 px-6 py-4 flex justify-between items-center rounded-b-2xl border-t border-slate-100 select-none">
                                        <span class="text-[10px] sm:text-xs font-bold text-slate-400">
                                            Ulasan {{ $index + 1 }} dari {{ $jumlahTestimoni }} Testimoni
                                        </span>
                                        <button @click="activeIndex = null"
                                            class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-5 text-xs rounded-xl transition cursor-pointer">
                                            Tutup
                                        </button>
                                    </div>

                                </div>

                                {{-- âž¡ï¸ TOMBOL NAVIGASI KANAN (NEXT) --}}
                                <button @click.stop="activeIndex = (activeIndex === totalItems - 1) ? 0 : activeIndex + 1"
                                    class="fixed right-4 md:right-8 top-1/2 -translate-y-1/2 z-50 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/90 text-slate-700 hover:bg-blue-600 hover:text-white flex items-center justify-center shadow-lg border border-slate-200/50 transition cursor-pointer">
                                    <i class="fas fa-chevron-right text-sm md:text-base"></i>
                                </button>

                            </div>
                        </div>

                    @empty
                        {{-- TAMPILAN CADANGAN JIKA DATA KOSONG --}}
                        <div
                            class="col-span-1 sm:col-span-2 lg:col-span-3 bg-white rounded-3xl border border-slate-200/60 p-12 text-center shadow-xs">
                            <div
                                class="w-16 h-16 bg-slate-50 text-slate-400 flex items-center justify-center text-2xl rounded-2xl mx-auto mb-4 border border-slate-100">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Belum Ada Ulasan</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">
                                Lembar ulasan testimoni alumni pelatihan belum diisi melalui panel admin balai.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection

