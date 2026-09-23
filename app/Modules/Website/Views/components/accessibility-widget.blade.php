<svg id="ax-cb-filters" style="position: absolute; height: 0; width: 0; overflow: hidden;" version="1.1"
    xmlns="http://www.w3.org/2000/svg">
    <defs>
        <filter id="ax-filter-protanopia">
            <feColorMatrix type="matrix"
                values="0.567, 0.433, 0, 0, 0, 0.558, 0.442, 0, 0, 0, 0, 0.242, 0.758, 0, 0, 0, 0, 0, 1, 0" />
        </filter>
        <filter id="ax-filter-deuteranopia">
            <feColorMatrix type="matrix"
                values="0.625, 0.375, 0, 0, 0, 0.7, 0.3, 0, 0, 0, 0, 0.3, 0.7, 0, 0, 0, 0, 0, 1, 0" />
        </filter>
        <filter id="ax-filter-tritanopia">
            <feColorMatrix type="matrix"
                values="0.95, 0.05, 0, 0, 0, 0, 0.433, 0.567, 0, 0, 0, 0.475, 0.525, 0, 0, 0, 0, 0, 1, 0" />
        </filter>
    </defs>
</svg>

{{-- 🏁 GARIS PANDU BACA --}}
<div x-data="{ isGuideActive: localStorage.getItem('ax-guide') === 'true' }" x-show="isGuideActive"
    @mousemove.window="document.getElementById('ax-reading-line').style.top = $event.clientY + 'px'"
    @ax-update-guide.window="isGuideActive = $event.detail.status" id="ax-reading-line"
    class="fixed left-0 right-0 h-1.5 bg-yellow-400 pointer-events-none z-[99999] shadow-md transition-all duration-75"
    style="top: 0px;" x-cloak>
</div>

<div x-data="{
    isOpen: false,
    textSize: localStorage.getItem('ax-text-size') || 'normal',
    highContrast: localStorage.getItem('ax-contrast') === 'true',
    dyslexiaFont: localStorage.getItem('ax-dyslexia') === 'true',
    underlineLinks: localStorage.getItem('ax-underline') === 'true',
    colorMode: localStorage.getItem('ax-color-mode') || 'normal',
    cbMode: localStorage.getItem('ax-cb-mode') || 'normal', // 🟢 State baru buta warna
    bigCursor: localStorage.getItem('ax-cursor') === 'true',
    readingGuide: localStorage.getItem('ax-guide') === 'true',
    screenReader: localStorage.getItem('ax-reader') === 'true',

    lastClickTime: 0,
    lastTarget: null,

    init() {
        this.applySettings();
        this.setupScreenReader();
    },

    setupScreenReader() {
        const self = this;
        document.addEventListener('click', function(e) {
            if (!self.screenReader) return;

            const interactive = e.target.closest('a, button, [role=button]');
            if (!interactive) {
                const textEl = e.target.closest('h1, h2, h3, h4, h5, h6, p, span, li');
                if (textEl) self.speak(textEl.innerText);
                return;
            }

            const currentTime = new Date().getTime();
            const lastClick = parseInt(interactive.dataset.lastClick || 0);
            const timeDiff = currentTime - lastClick;

            if (timeDiff < 500) {
                interactive.dataset.lastClick = 0;
            } else {
                e.preventDefault();
                e.stopPropagation();

                interactive.dataset.lastClick = currentTime;
                let textToSpeak = interactive.innerText || interactive.getAttribute('aria-label') || 'Tombol aksi';
                self.speak(textToSpeak + '. Ketuk dua kali dengan cepat untuk membuka.');
            }
        }, true);
    },

    speak(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            window.speechSynthesis.speak(utterance);
        }
    },

    toggleReader() {
        this.screenReader = !this.screenReader;
        localStorage.setItem('ax-reader', this.screenReader);
        if (this.screenReader) {
            this.speak('Asisten suara aktif. Klik sekali untuk mendengar, klik dua kali untuk membuka.');
        } else {
            if ('speechSynthesis' in window) window.speechSynthesis.cancel();
        }
    },

    toggleContrast() {
        this.highContrast = !this.highContrast;
        localStorage.setItem('ax-contrast', this.highContrast);
        if (this.highContrast) this.cbMode = 'normal'; // Reset buta warna jika kontras tinggi aktif
        this.applySettings();
    },

    toggleDyslexia() {
        this.dyslexiaFont = !this.dyslexiaFont;
        localStorage.setItem('ax-dyslexia', this.dyslexiaFont);
        this.applySettings();
    },

    toggleUnderline() {
        this.underlineLinks = !this.underlineLinks;
        localStorage.setItem('ax-underline', this.underlineLinks);
        this.applySettings();
    },

    toggleCursor() {
        this.bigCursor = !this.bigCursor;
        localStorage.setItem('ax-cursor', this.bigCursor);
        this.applySettings();
    },

    toggleGuide() {
        this.readingGuide = !this.readingGuide;
        localStorage.setItem('ax-guide', this.readingGuide);
        window.dispatchEvent(new CustomEvent('ax-update-guide', { detail: { status: this.readingGuide } }));
    },

    changeTextSize(size) {
        this.textSize = size;
        localStorage.setItem('ax-text-size', size);
        this.applySettings();
    },

    changeColorMode(mode) {
        this.colorMode = mode;
        localStorage.setItem('ax-color-mode', mode);
        if (mode !== 'normal') this.cbMode = 'normal'; // Reset buta warna jika filter dasar aktif
        this.applySettings();
    },

    changeColorBlindMode(mode) {
        this.cbMode = mode;
        localStorage.setItem('ax-cb-mode', mode);
        if (mode !== 'normal') {
            this.colorMode = 'normal';
            this.highContrast = false; // Matikan kontras agar filter SVG bekerja optimal
        }
        this.applySettings();
    },

    resetSettings() {
        this.textSize = 'normal';
        this.highContrast = false;
        this.dyslexiaFont = false;
        this.underlineLinks = false;
        this.colorMode = 'normal';
        this.cbMode = 'normal';
        this.bigCursor = false;
        this.readingGuide = false;
        this.screenReader = false;
        if ('speechSynthesis' in window) window.speechSynthesis.cancel();
        localStorage.clear();
        window.dispatchEvent(new CustomEvent('ax-update-guide', { detail: { status: false } }));
        this.applySettings();
    },

    applySettings() {
        const html = document.documentElement;

        html.classList.remove('ax-text-large', 'ax-text-xlarge');
        if (this.textSize === 'large') html.classList.add('ax-text-large');
        if (this.textSize === 'xlarge') html.classList.add('ax-text-xlarge');

        html.classList.remove('ax-grayscale', 'ax-sepia', 'ax-invert');
        if (this.colorMode !== 'normal') html.classList.add('ax-' + this.colorMode);

        // 🟢 Atur Suntikan Class Buta Warna
        html.classList.remove('ax-protanopia', 'ax-deuteranopia', 'ax-tritanopia');
        if (this.cbMode !== 'normal') html.classList.add('ax-' + this.cbMode);

        html.classList.toggle('ax-high-contrast', this.highContrast);
        html.classList.toggle('ax-dyslexia-font', this.dyslexiaFont);
        html.classList.toggle('ax-force-underline', this.underlineLinks);
        html.classList.toggle('ax-big-cursor', this.bigCursor);
    }
}" class="fixed bottom-6 left-6 z-[99999] font-sans select-none">

    {{-- ♿ TOMBOL UTAMA --}}
    <button @click="isOpen = !isOpen"
        class="w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-2xl transition duration-300 cursor-pointer focus:outline-hidden focus:ring-4 focus:ring-blue-300">
        <i class="fas fa-universal-access text-2xl" x-show="!isOpen"></i>
        <i class="fas fa-times text-xl" x-show="isOpen" x-cloak></i>
    </button>

    {{-- 📋 DASHBOARD MENU AKSESIBILITAS --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-10"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-10" @click.away="isOpen = false"
        class="absolute bottom-18 left-0 bg-white border border-slate-200 shadow-3xl rounded-3xl p-5 w-80 space-y-4 text-slate-800"
        x-cloak>

        <div class="border-b border-slate-100 pb-2">
            <h3 class="font-black text-sm tracking-tight text-slate-900 flex items-center gap-2">
                <i class="fas fa-wheelchair text-blue-600"></i> Mode Ramah Disabilitas
            </h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Asisten pembantu khusus Balai Pangkep</p>
        </div>

        {{-- SEKSI 1: AUDIO & NAVIGASI --}}
        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">🔊 Asisten Suara
                (Tunanetra):</span>
            <button @click="toggleReader()"
                :class="screenReader ? 'bg-emerald-600 text-white' :
                    'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'"
                class="w-full flex items-center justify-between p-2 rounded-lg text-xs font-bold transition cursor-pointer text-left">
                <span class="flex items-center gap-2"><i class="fas fa-volume-up text-sm"></i> Pembaca Suara & Ketuk
                    2x</span>
                <i class="fas"
                    :class="screenReader ? 'fa-toggle-on text-lg' : 'fa-toggle-off text-lg text-slate-300'"></i>
            </button>
        </div>

        {{-- SEKSI 2: FILTER WARNA DASAR & KONTRAS --}}
        <div class="space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">🎨 Filter Warna
                Dasar:</span>
            <div class="grid grid-cols-4 gap-1">
                <button @click="changeColorMode('normal')"
                    :class="colorMode === 'normal' && !highContrast ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1 rounded-lg text-[10px] font-bold cursor-pointer">Normal</button>
                <button @click="changeColorMode('grayscale')"
                    :class="colorMode === 'grayscale' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1 rounded-lg text-[10px] font-bold cursor-pointer">Mono</button>
                <button @click="changeColorMode('sepia')"
                    :class="colorMode === 'sepia' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1 rounded-lg text-[10px] font-bold cursor-pointer">Sepia</button>
                <button @click="changeColorMode('invert')"
                    :class="colorMode === 'invert' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1 rounded-lg text-[10px] font-bold cursor-pointer">Invert</button>
            </div>
        </div>

        {{-- 🟢 SEKSI BARU: FILTER KHUSUS BUTA WARNA --}}
        <div class="space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">👁️ Koreksi Buta Warna
                (Daltonisme):</span>
            <div class="grid grid-cols-2 gap-1.5">
                <button @click="changeColorBlindMode('normal')"
                    :class="cbMode === 'normal' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1.5 px-2 rounded-lg text-[10px] font-bold cursor-pointer text-center">
                    Achromatopsia
                </button>
                <button @click="changeColorBlindMode('protanopia')"
                    :class="cbMode === 'protanopia' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1.5 px-2 rounded-lg text-[10px] font-bold cursor-pointer text-center"
                    title="Buta warna merah">
                    Protan (Merah)
                </button>
                <button @click="changeColorBlindMode('deuteranopia')"
                    :class="cbMode === 'deuteranopia' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1.5 px-2 rounded-lg text-[10px] font-bold cursor-pointer text-center"
                    title="Buta warna hijau">
                    Deuteran (Hijau)
                </button>
                <button @click="changeColorBlindMode('tritanopia')"
                    :class="cbMode === 'tritanopia' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1.5 px-2 rounded-lg text-[10px] font-bold cursor-pointer text-center"
                    title="Buta warna biru-kuning">
                    Tritan (Biru)
                </button>
            </div>

            <button @click="toggleContrast()"
                :class="highContrast ? 'bg-blue-600 text-white' :
                    'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'"
                class="w-full flex items-center justify-between p-2 border rounded-xl text-xs font-bold transition duration-300 cursor-pointer text-left">
                <span class="flex items-center gap-2"><i class="fas fa-circle-half-stroke text-sm"></i> Kontras Hitam
                    Tinggi</span>
                <i class="fas"
                    :class="highContrast ? 'fa-toggle-on text-lg' : 'fa-toggle-off text-lg text-slate-300'"></i>
            </button>
        </div>

        {{-- SEKSI 4: UKURAN & BENTUK HURUF --}}
        <div class="space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">🔤 Penataan Huruf &
                Tautan:</span>
            <div class="grid grid-cols-3 gap-1">
                <button @click="changeTextSize('normal')"
                    :class="textSize === 'normal' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1 rounded-md text-xs font-bold cursor-pointer">A</button>
                <button @click="changeTextSize('large')"
                    :class="textSize === 'large' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1 rounded-md text-xs font-bold cursor-pointer">A+</button>
                <button @click="changeTextSize('xlarge')"
                    :class="textSize === 'xlarge' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                    class="py-1 rounded-md text-xs font-bold cursor-pointer">A++</button>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button @click="toggleDyslexia()"
                    :class="dyslexiaFont ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-700'"
                    class="p-2 border rounded-xl text-[11px] font-bold cursor-pointer text-left flex items-center gap-1">
                    <i class="fas fa-font"></i> Huruf Disleksia
                </button>
                <button @click="toggleUnderline()"
                    :class="underlineLinks ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-700'"
                    class="p-2 border rounded-xl text-[11px] font-bold cursor-pointer text-left flex items-center gap-1">
                    <i class="fas fa-underline"></i> Garis Bawah Link
                </button>
            </div>
        </div>

        {{-- SEKSI 5: ALAT BANTU VISUAL --}}
        <div class="space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">👁️ Alat Bantu
                Monitor:</span>
            <div class="grid grid-cols-2 gap-2">
                <button @click="toggleCursor()"
                    :class="bigCursor ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-700'"
                    class="p-2 border rounded-xl text-[11px] font-bold cursor-pointer text-left flex items-center gap-1">
                    <i class="fas fa-mouse-pointer"></i> Kursor Raksasa
                </button>
                <button @click="toggleGuide()"
                    :class="readingGuide ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-700'"
                    class="p-2 border rounded-xl text-[11px] font-bold cursor-pointer text-left flex items-center gap-1">
                    <i class="fas fa-grip-lines"></i> Garis Pandu Baca
                </button>
            </div>
        </div>

        {{-- RESET SETTING --}}
        <button @click="resetSettings()"
            class="w-full py-2 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl text-xs font-bold transition duration-300 cursor-pointer text-center block">
            <i class="fas fa-undo-alt"></i> Kembalikan Pengaturan Normal
        </button>

    </div>
</div>
