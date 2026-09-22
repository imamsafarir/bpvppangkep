<div
    style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px; text-align: center;">
    {{-- Container Barcode / QR Code --}}
    <div
        style="padding: 16px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 16px; display: inline-block;">
        <img src="{{ $qrBase64 }}" alt="QR Code"
            style="width: 220px; height: 220px; display: block; margin: 0 auto;" />
    </div>

    {{-- Detail Pegawai & Shortlink --}}
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;">{{ $shortlink->pegawai_name }}
        </h3>
        <p style="font-size: 13px; font-family: monospace; font-weight: 600; color: #4f46e5; margin: 0;">
            {{ $shortlink->short_url }}</p>
    </div>

    {{-- Action Buttons --}}
    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('shortlink.qr', $shortlink->code) }}" download
            style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background-color: #4f46e5; color: #ffffff; font-size: 13px; font-weight: 600; border-radius: 10px; text-decoration: none; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Unduh Barcode (SVG)</span>
        </a>

        <button type="button"
            onclick="navigator.clipboard.writeText('{{ $shortlink->short_url }}'); alert('Tautan berhasil disalin ke clipboard!');"
            style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background-color: #f1f5f9; color: #334155; font-size: 13px; font-weight: 600; border-radius: 10px; border: 1px solid #cbd5e1; cursor: pointer;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                </path>
            </svg>
            <span>Salin URL</span>
        </button>
    </div>
</div>
