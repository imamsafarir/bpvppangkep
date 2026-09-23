@props(['settings'])

{{-- PERBAIKAN: Menggunakan h-[60vh] di mobile agar lebih ringkas, dan h-[100dvh] penuh saat di desktop --}}
<section class="w-full relative overflow-hidden bg-slate-950 h-[60vh] sm:h-[100dvh]" x-data="{
    currentSlider: 0,
    totalSliders: {{ $settings?->sliders ? count($settings->sliders) : 1 }},
    autoplayInterval: null,
    startAutoplay() {
        if (this.totalSliders > 1 && !this.autoplayInterval) {
            this.autoplayInterval = setInterval(() => {
                this.currentSlider = (this.currentSlider + 1) % this.totalSliders
            }, 5000);
        }
    },
    stopAutoplay() {
        clearInterval(this.autoplayInterval);
        this.autoplayInterval = null;
    }
}"
    x-init="startAutoplay()" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">

    {{-- KONTEN GAMBAR COVER BANNER --}}
    <div class="w-full h-full relative">
        @if ($settings?->sliders && count($settings->sliders) > 0)
            @foreach ($settings->sliders as $index => $slide)
                <div x-show="currentSlider === {{ $index }}" x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 scale-102 blur-xs"
                    x-transition:enter-end="opacity-100 scale-100 blur-none"
                    x-transition:leave="transition ease-in duration-800" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="absolute inset-0 w-full h-full">
                    <img src="{{ asset('storage/' . $slide) }}" alt="Slider Banner"
                        class="w-full h-full object-cover object-center transform transition-transform duration-500">
                </div>
            @endforeach
        @else
            <div
                class="absolute inset-0 bg-gradient-to-br from-slate-900 to-blue-950 flex items-center justify-center text-white/40 text-sm">
                <i class="fas fa-images mr-2 text-base animate-pulse"></i> Belum ada gambar cover slider.
            </div>
        @endif
    </div>

    <div
        class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-slate-950/40 mix-blend-multiply z-10">
    </div>

    {{-- ENGINE UTAMA & ANIMASI KURSOR MESIN KETIK --}}
    <div class="absolute inset-0 z-20 flex items-center justify-center px-4 overflow-hidden" x-data="{
        text: '',
        words: ['Kompeten', 'Unggul', 'Siap Kerja', 'Berproduktivitas Tinggi'],
        wordIndex: 0,
        charIndex: 0,
        isDeleting: false,
        typeEffect() {
            let currentWord = this.words[this.wordIndex];
            if (this.isDeleting) {
                this.text = currentWord.substring(0, this.charIndex - 1);
                this.charIndex--;
            } else {
                this.text = currentWord.substring(0, this.charIndex + 1);
                this.charIndex++;
            }
            let typeSpeed = this.isDeleting ? 40 : 80;
            if (!this.isDeleting && this.charIndex === currentWord.length) {
                typeSpeed = 2200;
                this.isDeleting = true;
            } else if (this.isDeleting && this.text === '') {
                this.isDeleting = false;
                this.wordIndex = (this.wordIndex + 1) % this.words.length;
                typeSpeed = 400;
            }
            setTimeout(() => this.typeEffect(), typeSpeed);
        }
    }"
        x-init="setTimeout(() => typeEffect(), 1200)">

        <div class="absolute inset-0 pointer-events-none select-none z-0">
            <div class="absolute w-80 h-80 bg-blue-500/15 rounded-full blur-3xl -top-16 -left-16 animate-pulse"
                style="animation-duration: 7s;"></div>
            <div class="absolute w-[450px] h-[450px] bg-amber-500/5 rounded-full blur-3xl bottom-5 right-5 animate-pulse"
                style="animation-duration: 11s;"></div>
        </div>

        <div class="text-center max-w-7xl space-y-3 sm:space-y-5 z-10 px-4 w-full mx-auto overflow-hidden">
            <h1
                class="text-xs sm:text-2xl md:text-3xl lg:text-4xl font-extrabold tracking-tight text-white drop-shadow-[0_4px_12px_rgba(0,0,0,0.7)] uppercase">
                Selamat Datang di Situs Resmi</h1>
            <div
                class="font-black bg-gradient-to-r from-amber-300 via-yellow-100 to-amber-400 bg-clip-text text-transparent drop-shadow-[0_4px_10px_rgba(0,0,0,0.6)] leading-tight tracking-tight uppercase flex flex-col items-center gap-1 sm:gap-2 w-full">
                <span
                    class="block text-sm sm:text-xl md:text-2xl lg:text-4xl xl:text-5xl max-w-full tracking-tighter sm:tracking-tight break-words text-center">Balai
                    Pelatihan Vokasi dan Produktivitas</span>
                <span
                    class="block text-base sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl text-amber-400 font-black tracking-normal">Pangkajene
                    dan Kepulauan</span>
            </div>
            <div class="text-[11px] sm:text-base md:text-lg font-medium text-slate-200/90 tracking-wide">
                Mewujudkan Tenaga Kerja yang <span
                    class="text-amber-400 font-black border-r-3 border-amber-400 pl-1 pb-0.5 animate-caret transition-all"
                    x-text="text">Siap Kerja</span>
            </div>
            <div class="pt-3 border-t border-white/15 max-w-xs mx-auto opacity-90">
                <p class="text-[9px] sm:text-xs text-amber-300 font-mono font-bold uppercase tracking-widest">
                    Kementerian Ketenagakerjaan RI</p>
            </div>
        </div>
    </div>

    @if ($settings?->sliders && count($settings->sliders) > 1)
        {{-- PERBAIKAN: Menyesuaikan jarak bottom agar posisi bullet dot proporsional dengan tinggi mobile --}}
        <div class="absolute bottom-4 sm:bottom-10 left-0 right-0 flex justify-center gap-3 z-30">
            @foreach ($settings->sliders as $index => $slide)
                <button @click="currentSlider = {{ $index }}"
                    class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                    :class="currentSlider === {{ $index }} ? 'w-8 bg-amber-400' : 'w-2 bg-white/30 hover:bg-white/60'"></button>
            @endforeach
        </div>
    @endif
</section>
