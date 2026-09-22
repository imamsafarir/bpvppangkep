<div class="flex flex-col items-center justify-center p-4 text-center space-y-4">
    <div class="p-3 bg-white border border-slate-200 rounded-2xl shadow-sm inline-block">
        {!! $svg !!}
    </div>

    <div>
        <p class="text-sm font-bold text-slate-800">{{ $shortlink->pegawai_name }}</p>
        <p class="text-xs text-indigo-600 font-mono font-medium mt-0.5">{{ $shortlink->short_url }}</p>
    </div>

    <div class="flex items-center gap-2 pt-2">
        <a href="{{ route('shortlink.qr', $shortlink->code) }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Unduh QR Code (.svg)</span>
        </a>

        <button type="button"
            onclick="navigator.clipboard.writeText('{{ $shortlink->short_url }}'); alert('Tautan berhasil disalin!');"
            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                </path>
            </svg>
            <span>Salin URL</span>
        </button>
    </div>
</div>
