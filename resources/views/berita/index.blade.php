@extends('layouts.app')

@section('title', 'Berita & Kegiatan - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen" x-data="{
        searchQuery: '',
        currentTag: 'all',
        {{-- 1. Ambil data asli backend dan mapping struktur JSON untuk Alpine.js --}}
        items: {{ json_encode(
            ($berita_list ?? collect())->map(function ($item) {
                // Proteksi & parsing file_foto (Filament Multi-upload)
                $foto = $item->file_foto;
                if (is_string($foto)) {
                    $foto = json_decode($foto, true) ?? [$foto];
                }
                $gambar = is_array($foto) ? $foto[0] ?? null : $foto;
        
                // Proteksi & parsing tags milik penulis lain (jika disimpan dalam format JSON array/string)
                $rawTags = $item->tags ?? 'Berita';
                if (is_string($rawTags) && (str_starts_with($rawTags, '[') || str_starts_with($rawTags, '{'))) {
                    $parsedTags = json_decode($rawTags, true);
                    $tagFinal = is_array($parsedTags) ? $parsedTags[0] ?? 'Berita' : $rawTags;
                } elseif (is_array($rawTags)) {
                    $tagFinal = $rawTags[0] ?? 'Berita';
                } else {
                    $tagFinal = $rawTags;
                }
        
                return [
                    'id' => $item->id,
                    'judul' => $item->judul_berita,
                    'tag' => trim(strtolower($tagFinal)), // standarisasi untuk filter data
                    'tag_label' => trim($tagFinal), // label asli untuk tampilan badge
                    'ringkasan' => Str::limit(strip_tags($item->konten_berita), 120),
                    'gambar_url' => $gambar ? asset('storage/' . $gambar) : null,
                    'tanggal' => $item->created_at?->translatedFormat('d F Y') ?? 'Baru saja',
                    'url' => route('berita.show', $item->id),
                ];
            }),
        ) }},
    
        {{-- 2. Mengumpulkan list tags unik secara otomatis berdasarkan kiriman penulis --}}
        get availableTags() {
            let tagsSet = new Set();
            this.items.forEach(item => {
                if (item.tag_label) tagsSet.add(item.tag_label);
            });
            return Array.from(tagsSet);
        },
    
        {{-- 3. Logika gabungan: Filter Berdasarkan Tag + Pencarian Kata Kunci --}}
        get filteredItems() {
            return this.items.filter(item => {
                // Logika Filter Tag
                const matchesTag = this.currentTag === 'all' || item.tag_label.toLowerCase() === this.currentTag.toLowerCase();
    
                // Logika Filter Kolom Pencarian
                const matchesSearch = item.judul.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    item.ringkasan.toLowerCase().includes(this.searchQuery.toLowerCase());
    
                return matchesTag && matchesSearch;
            });
        }
    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb Navigation --}}
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-slate-500">Kabar Balai</span>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-blue-600">Berita</span>
            </nav>

            <div class="space-y-6">

                {{-- Judul Halaman --}}
                <div class="border-b border-slate-200 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Informasi
                        Terkini</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Berita & Kegiatan Balai</h1>
                </div>

                {{-- BAR ALAT BARU: Pencarian & Filter Kategori --}}
                <div
                    class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200/60 shadow-xs">

                    {{-- Input Kotak Pencarian --}}
                    <div class="relative w-full lg:max-w-xs">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 text-xs">
                            <i class="fas fa-search"></i>
                        </span>
                        <input x-model="searchQuery" type="text" placeholder="Cari judul atau isi berita..."
                            class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all shadow-2xs">
                    </div>

                    {{-- Urutan Filter Pill Tabs Berdasarkan Tags Aktif Milik Penulis --}}
                    <div class="flex flex-wrap items-center gap-1.5 select-none">
                        <span
                            class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 hidden sm:inline-block">Filter
                            Tags:</span>

                        <button @click="currentTag = 'all'"
                            :class="currentTag === 'all' ? 'bg-blue-600 text-white font-bold shadow-xs' :
                                'bg-white text-slate-600 font-semibold border border-slate-200 hover:bg-slate-100/80'"
                            class="text-xs px-3.5 py-2 rounded-xl transition-all cursor-pointer">
                            🌐 Semua
                        </button>

                        {{-- Loop Otomatis Generate Kategori/Tag Penulis Lain --}}
                        <template x-for="tag in availableTags" :key="tag">
                            <button @click="currentTag = tag"
                                :class="currentTag === tag ? 'bg-blue-600 text-white font-bold shadow-xs' :
                                    'bg-white text-slate-600 font-semibold border border-slate-200 hover:bg-slate-100/80'"
                                class="text-xs px-3.5 py-2 rounded-xl transition-all cursor-pointer" x-text="'# ' + tag">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Grid Tampilan Berita Interaktif --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="filteredItems.length > 0">
                    <template x-for="item in filteredItems" :key="item.id">
                        <a :href="item.url"
                            class="bg-white rounded-2xl overflow-hidden border border-slate-200/60 shadow-xs group flex flex-col justify-between transition-all hover:shadow-md">
                            <div>
                                <div class="aspect-video bg-slate-200 overflow-hidden relative border-b border-slate-100">
                                    <template x-if="item.gambar_url">
                                        <img :src="item.gambar_url" :alt="item.judul"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    </template>
                                    <template x-if="!item.gambar_url">
                                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-3xl">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                    </template>
                                </div>
                                <div class="p-5 space-y-2">
                                    {{-- Tampilkan Badge Tag Dinamis --}}
                                    <span
                                        class="text-[9px] font-black uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-sm border border-blue-100/50 inline-block"
                                        x-text="item.tag_label"></span>

                                    <h3 class="font-extrabold text-slate-900 text-base leading-tight group-hover:text-blue-600 transition-colors line-clamp-2"
                                        x-text="item.judul"></h3>
                                    <p class="text-xs text-slate-500 line-clamp-2 font-normal leading-relaxed"
                                        x-text="item.ringkasan"></p>
                                </div>
                            </div>
                            <div class="px-5 pb-5 pt-2 text-[11px] text-slate-400 font-medium select-none"
                                x-text="item.tanggal"></div>
                        </a>
                    </template>
                </div>

                {{-- Tampilan Jika Hasil Filter atau Keyword Pencarian Tidak Menemukan Data --}}
                <div x-show="filteredItems.length === 0" style="display: none;"
                    class="col-span-full bg-white rounded-3xl border border-dashed border-slate-200 p-16 text-center shadow-xs">
                    <div
                        class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-4 border border-slate-100">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 text-base">Berita Tidak Ditemukan</h4>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 leading-relaxed">
                        Tidak ada arsip berita yang sesuai dengan kata kunci pencarian atau pilihan tag filter saat ini.
                    </p>
                </div>

            </div>
        </div>
    </main>
@endsection
