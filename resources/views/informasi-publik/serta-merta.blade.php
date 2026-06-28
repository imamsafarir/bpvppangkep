@extends('layouts.app')

@section('title', 'Serta Merta - ' . ($settings?->website_name ?? 'BPVP Pangkep'))

@section('content')
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Informasi Publik</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600">Informasi Serta Merta</span>
            </nav>

            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6"
                x-data="{
                    search: '',
                    sortBy: 'newest',
                    items: {{ json_encode(
                        $dokumen->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'nama' => $item->nama_dokumen,
                                'deskripsi' => $item->deskripsi ?? ($item->deskripsi_singkat ?? '-'),
                                'tanggal_raw' => $item->created_at->timestamp,
                                'tanggal_formatted' => $item->created_at->translatedFormat('d F Y'),
                                'url_lihat' => asset('storage/' . $item->file_path),
                                'url_unduh' => route('download.informasi', $item->id),
                            ];
                        }),
                    ) }},
                    get filteredItems() {
                        let filtered = this.items.filter(item => {
                            return item.nama.toLowerCase().includes(this.search.toLowerCase()) ||
                                item.deskripsi.toLowerCase().includes(this.search.toLowerCase());
                        });
                        if (this.sortBy === 'newest') {
                            return filtered.sort((a, b) => b.tanggal_raw - a.tanggal_raw);
                        } else {
                            return filtered.sort((a, b) => a.tanggal_raw - b.tanggal_raw);
                        }
                    }
                }">

                <div
                    class="border-b border-slate-100 pb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Keterbukaan
                            Informasi</span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Informasi Yang Wajib
                            Disediakan Serta Merta</h1>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <div class="relative flex-1 sm:w-64">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" x-model="search" placeholder="Cari dokumen..."
                                class="w-full text-xs pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                        </div>
                        <div class="relative">
                            <i
                                class="fas fa-sort-amount-down absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select x-model="sortBy"
                                class="text-xs pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white transition-all appearance-none cursor-pointer font-medium">
                                <option value="newest">Terbaru</option>
                                <option value="oldest">Terlama</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                                <th class="py-4 px-6 w-12 text-center">No</th>
                                <th class="py-4 px-6">Nama Dokumen / Informasi</th>
                                <th class="py-4 px-6 w-40">Tanggal Unggah</th>
                                <th class="py-4 px-6 w-44 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-50">
                            <template x-for="(item, index) in filteredItems" :key="item.id">
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6 text-center font-medium text-slate-400" x-text="index + 1"></td>
                                    <td class="py-4 px-6 space-y-0.5">
                                        <div class="font-bold text-slate-900" x-text="item.nama"></div>
                                        <div class="text-[11px] text-slate-400" x-text="item.deskripsi"></div>
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 font-medium" x-text="item.tanggal_formatted"></td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a :href="item.url_lihat" target="_blank"
                                                class="inline-flex items-center gap-1 px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold transition-all text-[11px]">
                                                <i class="fas fa-eye text-[10px]"></i> Lihat
                                            </a>
                                            <a :href="item.url_unduh"
                                                class="inline-flex items-center gap-1 px-2 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg font-bold transition-all text-[11px]">
                                                <i class="fas fa-download text-[10px]"></i> Unduh
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div x-show="filteredItems.length === 0"
                    class="text-center py-12 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                    <div
                        class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-lg mx-auto mb-3">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Dokumen</h4>
                    <p class="text-xs text-slate-400 mt-0.5"
                        x-text="search ? 'Tidak ada dokumen yang cocok dengan kata kunci.' : 'Daftar berkas informasi belum diunggah.'">
                    </p>
                </div>

            </div>
        </div>
    </main>
@endsection
