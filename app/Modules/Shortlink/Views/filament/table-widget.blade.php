@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Widgets\View\WidgetsRenderHook;
@endphp

<x-filament-widgets::widget class="fi-wi-table" style="position: relative; overflow: hidden;">
    {{ FilamentView::renderHook(WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}

    <style>
        @keyframes bpvp-table-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    {{-- Indikator Loading Modern di Tengah Tabel (Aktif saat Ganti Tampilan 50, Sort, Paginate, Search, Filter) --}}
    <div wire:loading.flex
         style="display: none; position: absolute; inset: 0; z-index: 50; align-items: center; justify-content: center; background-color: rgba(255, 255, 255, 0.82); backdrop-filter: blur(2px); border-radius: 0.75rem; transition: all 0.2s ease;">
        <div style="display: flex; align-items: center; gap: 12px; padding: 14px 24px; background-color: #ffffff; border-radius: 14px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0;">
            <svg style="width: 24px; height: 24px; animation: bpvp-table-spin 0.8s linear infinite; color: #4f46e5; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path style="opacity: 0.85;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div style="display: flex; flex-direction: column; text-align: left;">
                <span style="font-size: 14px; font-weight: 700; color: #0f172a; line-height: 1.3; font-family: ui-sans-serif, system-ui, sans-serif;">
                    Memuat Data...
                </span>
                <span style="font-size: 12px; color: #64748b; font-family: ui-sans-serif, system-ui, sans-serif; margin-top: 2px;">
                    Menyinkronkan data tampilan
                </span>
            </div>
        </div>
    </div>

    {{ $this->table ?? null }}

    {{ FilamentView::renderHook(WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
</x-filament-widgets::widget>
