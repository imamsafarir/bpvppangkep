<x-filament-panels::page class="relative">
    {{-- Global Loading State saat Submit Form Generate Shortlink & Barcode --}}
    <div wire:loading.delay.shortest wire:target="save" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-gray-900/40 backdrop-blur-sm transition-all duration-200">
        <div class="flex items-center gap-4 px-7 py-4 bg-white dark:bg-gray-800 shadow-2xl border border-gray-200 dark:border-gray-700 rounded-2xl ring-1 ring-black/10 dark:ring-white/10">
            <svg class="animate-spin h-7 w-7 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div class="flex flex-col">
                <span class="text-base font-bold text-gray-800 dark:text-gray-100">
                    Membuat Shortlink & Barcode...
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Sistem sedang men-generate kode unik & QR code
                </span>
            </div>
        </div>
    </div>

    {{-- Render Form Input Shortlink & Barcode --}}
    {{ $this->form }}
</x-filament-panels::page>
