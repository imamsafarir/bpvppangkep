@props(['berita'])
<section id="berita-home" class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-10 pb-5 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Kabar
                    Balai</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Berita & Artikel Terkini
                </h2>
            </div>
            <a href="{{ route('berita.index') }}"
                class="text-xs font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wider flex items-center gap-1 transition-colors">
                Lihat Semua Berita <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        {{-- Layout Grid Berita Ala Media Online --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            @if (isset($berita) && $berita->count() > 0)
                @php $beritaUtama = $berita->first(); @endphp

                {{-- Kiri: Berita Utama Besar (Highlight) --}}
                <div class="lg:col-span-7 group relative flex flex-col space-y-4 break-words min-w-0">
                    <div
                        class="w-full aspect-video bg-slate-100 rounded-2xl overflow-hidden border border-slate-200/50 relative shadow-xs">
                        @if ($beritaUtama->file_foto)
                            <img src="{{ asset('storage/' . (is_array($beritaUtama->file_foto) ? $beritaUtama->file_foto[0] : json_decode($beritaUtama->file_foto)[0] ?? $beritaUtama->file_foto)) }}"
                                alt="Berita Utama"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500 ease-out">
                        @endif
                        <div class="absolute top-4 left-4 z-20">
                            <span
                                class="text-[10px] font-black uppercase tracking-wider bg-blue-600 text-white px-3 py-1 rounded-md shadow-xs select-none">Sorotan</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 text-xs text-slate-400 font-medium select-none">
                            <span>{{ $beritaUtama->created_at?->translatedFormat('d M Y') }}</span>
                            @if (!empty($beritaUtama->tags))
                                <span
                                    class="text-blue-600 font-bold">#{{ is_array($beritaUtama->tags) ? $beritaUtama->tags[0] : $beritaUtama->tags }}</span>
                            @endif
                        </div>
                        <a href="{{ route('berita.show', $beritaUtama->id) }}" class="block">
                            <h3
                                class="text-xl sm:text-2xl font-black text-slate-900 leading-tight hover:text-blue-600 transition-colors line-clamp-2 break-words">
                                {{ $beritaUtama->judul_berita }}
                            </h3>
                        </a>
                        {{-- FIX OFFSIDE: Menggunakan break-words, font-normal, dan limitasi baris line-clamp --}}
                        <p
                            class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal line-clamp-3 break-words whitespace-normal">
                            {{ strip_tags($beritaUtama->konten_berita) }}
                        </p>
                    </div>
                </div>

                {{-- Kanan: Daftar 3 Berita List Kecil --}}
                <div class="lg:col-span-5 space-y-6 divide-y divide-slate-100 lg:divide-y-0 min-w-0">
                    @foreach ($berita->skip(1)->take(3) as $subBerita)
                        <div class="flex gap-4 items-center pt-4 first:pt-0 lg:pt-0 group break-words min-w-0">
                            <div
                                class="w-24 sm:w-28 aspect-video bg-slate-100 rounded-xl overflow-hidden flex-shrink-0 border border-slate-200/40 shadow-2xs">
                                @if ($subBerita->file_foto)
                                    <img src="{{ asset('storage/' . (is_array($subBerita->file_foto) ? $subBerita->file_foto[0] : json_decode($subBerita->file_foto)[0] ?? $subBerita->file_foto)) }}"
                                        alt="Thumb"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300 ease-out">
                                @endif
                            </div>
                            <div class="space-y-1 min-w-0 flex-1">
                                <span
                                    class="text-[10px] font-bold text-slate-400 block select-none">{{ $subBerita->created_at?->translatedFormat('d M Y') }}</span>
                                <a href="{{ route('berita.show', $subBerita->id) }}" class="block">
                                    <h4
                                        class="font-extrabold text-xs sm:text-sm text-slate-900 leading-snug hover:text-blue-600 transition-colors line-clamp-2 break-words">
                                        {{ $subBerita->judul_berita }}
                                    </h4>
                                </a>
                                {{-- FIX OFFSIDE LIST KECIL: Proteksi pemotongan teks bersambung tanpa spasi --}}
                                <p
                                    class="text-[11px] text-slate-400 font-normal line-clamp-1 break-words whitespace-normal">
                                    {{ strip_tags($subBerita->konten_berita) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div
                    class="col-span-full text-center py-12 border border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                    <p class="text-slate-400 italic text-sm">Belum ada unggahan artikel berita di backend.</p>
                </div>
            @endif
        </div>
    </div>
</section>
