@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Widgets\View\WidgetsRenderHook;
@endphp

<x-filament-widgets::widget class="fi-wi-table relative overflow-hidden">
    {{ FilamentView::renderHook(WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}

    {{-- Indikator Loading Modern di Tengah Tabel (Muncul saat Ganti Tampilan 50, Sort, Paginate, Search, Filter) --}}
    <div wire:loading.delay.shortest class="absolute inset-0 z-40 flex flex-col items-center justify-center bg-white/75 dark:bg-gray-900/80 backdrop-blur-[2px] transition-all duration-200 rounded-xl">
        <div class="flex items-center gap-3.5 px-6 py-4 bg-white dark:bg-gray-800 shadow-2xl border border-gray-200/80 dark:border-gray-700/80 rounded-2xl ring-1 ring-black/5 dark:ring-white/10 animate-in fade-in zoom-in-95 duration-150">
            <svg class="animate-spin h-6 w-6 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wide flex items-center gap-1.5">
                    Memuat Data...
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Menyinkronkan data tampilan
                </span>
            </div>
        </div>
    </div>

    {{ $this->table ?? null }}

    {{ FilamentView::renderHook(WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
</x-filament-widgets::widget>
