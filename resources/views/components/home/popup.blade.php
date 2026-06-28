@props(['settings'])

@if ($settings?->is_popup_active && $settings->popup_image_path)
    <div x-data="{
        showPopup: true,
        timeLeft: 10,
        timer: null,
        startTimer() {
            this.timer = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    this.closePopup();
                }
            }, 1000);
        },
        closePopup() {
            this.showPopup = false;
            clearInterval(this.timer);
        }
    }" x-init="startTimer()" x-show="showPopup" x-transition.opacity.duration.1000ms
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm transition-opacity duration-300">

        <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden max-w-md w-full relative shadow-2xl border border-slate-100/80 dark:border-slate-800 flex flex-col"
            @click.away="closePopup()">

            {{-- Tombol Tutup Manual (Tetap di pojok kanan atas gambar) --}}
            <button @click="closePopup()"
                class="absolute top-4 right-4 bg-black/40 hover:bg-black/70 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm transition shadow-md z-20">
                ✕
            </button>

            {{-- Wadah Gambar (Murni tanpa tumpukan teks) --}}
            <div class="w-full overflow-hidden">
                @if ($settings->popup_redirect_url)
                    <a href="{{ $settings->popup_redirect_url }}" target="_blank" class="block">
                        <img src="{{ asset('storage/' . $settings->popup_image_path) }}" alt="Iklan Pengumuman"
                            class="w-full h-auto object-cover max-h-[65vh]">
                    </a>
                @else
                    <img src="{{ asset('storage/' . $settings->popup_image_path) }}" alt="Iklan Pengumuman"
                        class="w-full h-auto object-cover max-h-[65vh]">
                @endif
            </div>

            {{-- BARIS HITUNG MUNDUR DI LUAR GAMBAR (Bertindak sebagai footer kartu) --}}
            <div
                class="bg-slate-50 dark:bg-slate-950/40 px-4 py-3.5 border-t border-slate-100 dark:border-slate-800 text-center select-none flex-shrink-0">
                <p class="text-xs font-bold text-slate-500 dark:text-zinc-400 tracking-wide">
                    Otomatis tertutup dalam <span x-text="timeLeft"
                        class="text-amber-500 dark:text-amber-400 text-sm font-black mx-1"></span> detik
                </p>
            </div>

        </div>
    </div>
@endif
