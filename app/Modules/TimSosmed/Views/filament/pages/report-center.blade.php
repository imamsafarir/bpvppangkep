<x-filament-panels::page>
    {{-- HAPUS div fi-sc fi-grid --}}
    {{-- Langsung masukkan elemen di sini agar dia berada di dalam fi-page-content --}}

    {{-- 1. FRAME GRAFIK (Flat di dalam page content) --}}
    <section
        class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        {{-- Container Geser Khusus Mobile --}}
        <div class="overflow-x-auto p-4" style="-webkit-overflow-scrolling: touch;">
            {{-- Lebar 400px di HP (agar bisa digeser), 100% di Desktop --}}
            <div class="w-[400px] md:w-full">
                @livewire(
                    \App\Modules\TimSosmed\Filament\Widgets\ContentChart::class,
                    [
                        'height' => '200px',
                        'showHeader' => false,
                    ],
                    key('report-chart-' . now()->timestamp)
                )
            </div>
        </div>
    </section>

    {{-- 2. FRAME TABEL --}}
    <div class="mt-6"> {{-- Beri jarak manual karena grid gap sudah dihapus --}}
        <x-filament::section icon="heroicon-m-list-bullet" collapsible>
            <x-slot name="heading">Daftar Konten Selesai</x-slot>
            <x-slot name="description">
                Centang baris di bawah untuk aksi massal.
            </x-slot>

            <div class="mt-4 overflow-x-auto -mx-4 md:mx-0">
                <div class="inline-block min-w-full align-middle px-4 md:px-0">
                    {{ $this->table }}
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- 3. FRAME TIPS --}}
    <div class="mt-6">
        <x-filament::callout icon="heroicon-m-light-bulb" color="info">
            <x-slot name="heading">Tips Penggunaan</x-slot>
            <x-slot name="description">
                Cari nama Anda di kolom pencarian, centang, lalu pilih
                <span class="font-bold">"Download PDF Terpilih"</span>.
            </x-slot>
        </x-filament::callout>
    </div>

</x-filament-panels::page>
