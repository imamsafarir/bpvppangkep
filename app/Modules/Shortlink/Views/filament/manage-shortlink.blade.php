<x-filament-panels::page>
    <style>
        @keyframes bpvp-page-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    {{-- Global Loading State saat Submit Form Generate Shortlink & Barcode --}}
    <div wire:loading.flex
         wire:target="save"
         style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center; background-color: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px);">
        <div style="display: flex; align-items: center; gap: 16px; padding: 18px 30px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0;">
            <svg style="width: 28px; height: 28px; animation: bpvp-page-spin 0.8s linear infinite; color: #4f46e5; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path style="opacity: 0.85;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div style="display: flex; flex-direction: column; text-align: left;">
                <span style="font-size: 15px; font-weight: 700; color: #0f172a; line-height: 1.3; font-family: ui-sans-serif, system-ui, sans-serif;">
                    Membuat Shortlink & Barcode...
                </span>
                <span style="font-size: 12px; color: #64748b; font-family: ui-sans-serif, system-ui, sans-serif; margin-top: 2px;">
                    Sistem sedang men-generate kode unik & QR code
                </span>
            </div>
        </div>
    </div>

    {{-- Render Form Input Shortlink & Barcode --}}
    {{ $this->form }}
</x-filament-panels::page>
