<style>
    [x-cloak] {
        display: none !important;
    }

    .animate-marquee {
        display: inline-flex;
        width: max-content;
        min-width: 100%;
        padding-left: 100%;
        animation: marqueeAnimation 100s linear infinite;
        will-change: transform;
    }

    @keyframes marqueeAnimation {
        0% {
            transform: translate3d(0, 0, 0);
        }

        100% {
            transform: translate3d(-100%, 0, 0);
        }
    }
</style>

<header id="navbar-container" class="fixed top-0 left-0 z-40 w-full transition-all duration-500" x-data="{
    openMenu: null,
    mobileMenu: false,
    isScrolled: window.scrollY > 20
}"
    @scroll.window="isScrolled = window.scrollY > 20"
    @keydown.escape.window="
        openMenu = null;
        mobileMenu = false;
    ">
    {{-- ============================================================= --}}
    {{-- RUNNING TEXT --}}
    {{-- ============================================================= --}}
    @if ($settings?->is_running_text_active && $settings->running_text_content)
        <div class="relative z-50 w-full select-none overflow-hidden border-b border-white/10 py-2.5 text-white shadow-sm"
            style="background-color: #1b446f;">
            <div class="animate-marquee items-center whitespace-nowrap text-xs font-medium uppercase tracking-wide">
                <span class="mx-4 shrink-0 font-bold text-amber-300">
                    ⚠️ INFO TERKINI:
                </span>

                <span class="shrink-0 pr-10">
                    {{ $settings->running_text_content }}
                </span>
            </div>
        </div>
    @endif

    {{-- ============================================================= --}}
    {{-- NAVBAR UTAMA --}}
    {{-- ============================================================= --}}
    <div id="navbar-main"
        class="w-full border-b py-3.5 transition-all duration-500
            {{ Route::is('home') ? '' : 'border-amber-400/30 bg-[#15406a] shadow-md' }}"
        @if (Route::is('home')) :class="isScrolled
                ? 'border-amber-400/30 bg-[#15406a] shadow-md'
                : 'border-transparent bg-transparent shadow-none'" @endif>
        <div class="mx-auto flex w-full max-w-[1700px] items-center justify-between gap-3 px-3 sm:px-4 lg:px-5 xl:px-6">
            {{-- ===================================================== --}}
            {{-- LOGO DAN NAMA WEBSITE --}}
            {{-- ===================================================== --}}
            <a href="/" class="flex shrink-0 select-none items-center gap-2 text-white"
                aria-label="Beranda {{ $settings?->website_name ?? 'BPVP Pangkep' }}">
                <img src="{{ $settings?->logo_path ? asset('storage/' . $settings->logo_path) : asset('images/default-logo.png') }}"
                    alt="{{ $settings?->website_name ?? '' }}" class="h-9 w-auto shrink-0 object-contain xl:h-10">

                <div class="shrink-0 leading-tight">
                    <span
                        class="block whitespace-nowrap text-sm font-black tracking-tight drop-shadow-md sm:text-base 2xl:text-lg">
                        {{ $settings?->website_name ?? 'BPVP PANGKEP' }}
                    </span>

                    <span
                        class="block whitespace-nowrap text-[8px] font-bold uppercase tracking-wider text-amber-400 sm:text-[9px]">
                        KEMNAKER RI
                    </span>
                </div>
            </a>

            {{-- ===================================================== --}}
            {{-- DATA MENU --}}
            {{-- ===================================================== --}}
            @php
                $navigationMenu = [
                    'Profil' => [
                        'type' => 'dropdown',
                        'key' => 'profil',
                        'width' => 'w-52',
                        'links' => [
                            'Sambutan Kepala' => route('profil.sambutan'),
                            'Tentang Kami' => route('profil.tentang-kami'),
                            'PPID' => route('profil.ppid'),
                            'Visi & Misi' => route('profil.visi-misi'),
                            'Tugas & Fungsi' => route('profil.tugas-fungsi'),
                            'Struktur Organisasi' => route('profil.struktur'),
                            'Pejabat Struktural' => route('profil.pejabat'),
                        ],
                    ],

                    'Informasi' => [
                        'type' => 'dropdown',
                        'key' => 'informasi',
                        'width' => 'w-56',
                        'links' => [
                            'Kejuruan' => route('informasi.kejuruan'),
                            'Gedung & Fasilitas' => route('informasi.fasilitas'),
                            'Ruang Kelas & Workshop' => route('informasi.workshop'),
                            'Alumni' => route('informasi.alumni'),
                            'Testimoni' => route('informasi.testimoni'),
                        ],
                    ],

                    'Informasi Publik' => [
                        'type' => 'dropdown',
                        'key' => 'infopublik',
                        'width' => 'w-56',
                        'links' => [
                            'Informasi Berkala' => route('publik.berkala'),
                            'Informasi Serta Merta' => route('publik.serta-merta'),
                            'Informasi Setiap Saat' => route('publik.setiap-saat'),
                        ],
                    ],

                    'Pelayanan Publik' => [
                        'type' => 'dropdown',
                        'key' => 'pelayanan',
                        'width' => 'w-64',
                        'links' => [
                            'Maklumat Pelayanan' => route('pelayanan.maklumat'),
                            'Standar Pelayanan Publik' => route('pelayanan.standar'),
                            'Alur Pelayanan' => route('pelayanan.alur'),
                            'Survey Kepuasan Masyarakat' => route('pelayanan.survey-kepuasan'),
                            'Survey Kebutuhan Pelatihan' => route('pelayanan.survey-kebutuhan'),
                            'Survey Kebekerjaan' => route('pelayanan.survey-kebekerjaan'),
                            'Indeks Kepuasan Masyarakat' => route('pelayanan.indeks-kepuasan'),
                        ],
                    ],

                    'Berita' => [
                        'type' => 'dropdown',
                        'key' => 'berita',
                        'width' => 'w-48',
                        'links' => [
                            'Berita' => route('berita.index'),
                            'Galeri Kegiatan' => route('berita.galeri'),
                        ],
                    ],

                    'JDIH' => [
                        'type' => 'link',
                        'url' => route('jdih.index'),
                    ],
                ];
            @endphp

            {{-- ===================================================== --}}
            {{-- NAVIGASI DESKTOP --}}
            {{-- ===================================================== --}}
            <nav
                class="ml-auto hidden min-w-0 flex-1 flex-nowrap items-center justify-end gap-2 whitespace-nowrap
                    text-[11px] font-semibold text-white lg:flex xl:gap-3 xl:text-xs 2xl:gap-5 2xl:text-sm">
                <a href="/"
                    class="shrink-0 whitespace-nowrap text-white/90 drop-shadow-sm transition-colors duration-200 hover:text-amber-400">
                    Beranda
                </a>

                @foreach ($navigationMenu as $title => $menu)
                    @if ($menu['type'] === 'dropdown')
                        <div class="relative shrink-0 py-2" @mouseenter="openMenu = '{{ $menu['key'] }}'"
                            @mouseleave="openMenu = null" @click.away="openMenu = null">
                            <button type="button"
                                @click="
                                    openMenu = openMenu === '{{ $menu['key'] }}'
                                        ? null
                                        : '{{ $menu['key'] }}'
                                "
                                class="flex shrink-0 cursor-pointer items-center gap-1 whitespace-nowrap
                                    text-white/90 drop-shadow-sm transition-colors duration-200
                                    hover:text-amber-400 focus:outline-none"
                                :aria-expanded="openMenu === '{{ $menu['key'] }}'">
                                <span class="whitespace-nowrap">
                                    {{ $title }}
                                </span>

                                <svg class="h-3.5 w-3.5 shrink-0 transition-transform duration-200"
                                    :class="openMenu === '{{ $menu['key'] }}'
                                        ?
                                        'rotate-180 text-amber-400' :
                                        ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown --}}
                            <div x-cloak x-show="openMenu === '{{ $menu['key'] }}'"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="translate-y-1 opacity-0"
                                x-transition:enter-end="translate-y-0 opacity-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="translate-y-0 opacity-100"
                                x-transition:leave-end="translate-y-1 opacity-0"
                                class="absolute left-0 z-50 mt-2 {{ $menu['width'] }}
                                    overflow-hidden rounded-xl border border-slate-100
                                    bg-white py-2 text-slate-800 shadow-xl">
                                @foreach ($menu['links'] as $label => $url)
                                    <a href="{{ $url }}"
                                        class="block whitespace-nowrap border-l-2 border-transparent
                                            px-4 py-2 text-xs font-semibold transition-all duration-200
                                            hover:border-amber-500 hover:bg-amber-50
                                            hover:text-blue-900 2xl:text-sm">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $menu['url'] }}"
                            class="shrink-0 whitespace-nowrap text-white/90 drop-shadow-sm
                                transition-colors duration-200 hover:text-amber-400">
                            {{ $title }}
                        </a>
                    @endif
                @endforeach

                {{-- Tombol WhatsApp --}}
                <a href="https://api.whatsapp.com/send?phone={{ $settings?->whatsapp_number ?? '6285343747243' }}&text=Halo%20BPVP%20Pangkep..."
                    target="_blank" rel="noopener noreferrer"
                    class="flex shrink-0 items-center justify-center whitespace-nowrap rounded-xl
                        bg-amber-400 px-2.5 py-2 text-[10px] font-black text-slate-900
                        shadow-md shadow-amber-500/10 transition-all duration-200
                        hover:bg-amber-500 active:scale-95 xl:px-3 xl:text-[11px]
                        2xl:px-4 2xl:text-sm">
                    <i class="fab fa-whatsapp mr-1 hidden xl:inline-block"></i>

                    Hubungi Kami
                </a>

                {{-- Tombol SP4N LAPOR --}}
                <a href="https://www.lapor.go.id/" target="_blank" rel="noopener noreferrer"
                    class="flex shrink-0 items-center justify-center rounded-xl
                        bg-red-600 px-2.5 py-2 shadow-md shadow-red-600/20
                        transition-all duration-200 hover:bg-red-700
                        active:scale-95 xl:px-3">
                    <img src="https://www.lapor.go.id/themes/lapor/assets/images/logo-white.png" alt="SP4N LAPOR!"
                        class="h-4 w-auto shrink-0 object-contain xl:h-5 2xl:h-6">
                </a>
            </nav>

            {{-- ===================================================== --}}
            {{-- TOMBOL MOBILE --}}
            {{-- ===================================================== --}}
            <button type="button"
                @click="
                    mobileMenu = !mobileMenu;
                    openMenu = null;
                "
                class="shrink-0 cursor-pointer rounded-lg p-2 text-xl text-white
                    transition-colors hover:bg-white/10 focus:outline-none lg:hidden"
                aria-label="Buka menu navigasi" :aria-expanded="mobileMenu">
                <i class="fas"
                    :class="mobileMenu
                        ?
                        'fa-times text-amber-400' :
                        'fa-bars'"></i>
            </button>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- NAVIGASI MOBILE --}}
    {{-- ============================================================= --}}
    <div x-cloak x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-2 opacity-0"
        class="absolute left-0 z-50 w-full border-t border-amber-400/30
            bg-[#15406a] px-5 pt-2 pb-6 text-white shadow-xl lg:hidden"
        x-data="{ activeTab: null }">
        <div class="mx-auto max-h-[calc(100vh-120px)] max-w-2xl overflow-y-auto">
            <a href="/" @click="mobileMenu = false"
                class="block border-b border-white/10 py-3 text-sm font-bold text-amber-400">
                Beranda
            </a>

            @foreach ($navigationMenu as $title => $menu)
                @if ($menu['type'] === 'dropdown')
                    <div class="border-b border-white/10">
                        <button type="button"
                            @click="
                                activeTab = activeTab === '{{ $menu['key'] }}'
                                    ? null
                                    : '{{ $menu['key'] }}'
                            "
                            class="flex w-full cursor-pointer items-center justify-between
                                py-3 text-sm font-semibold text-slate-200
                                transition-colors focus:outline-none"
                            :class="activeTab === '{{ $menu['key'] }}'
                                ?
                                'text-amber-400' :
                                ''">
                            <span>
                                {{ $title }}
                            </span>

                            <i class="fas text-xs transition-transform duration-200"
                                :class="activeTab === '{{ $menu['key'] }}'
                                    ?
                                    'fa-chevron-up text-amber-400' :
                                    'fa-chevron-down'"></i>
                        </button>

                        <div x-cloak x-show="activeTab === '{{ $menu['key'] }}'" x-collapse
                            class="ml-1 border-l border-amber-400/40 pb-3 pl-4">
                            <div class="space-y-1">
                                @foreach ($menu['links'] as $label => $url)
                                    <a href="{{ $url }}" @click="mobileMenu = false"
                                        class="block py-1.5 text-xs font-medium text-slate-300
                                            transition-colors hover:text-amber-400">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ $menu['url'] }}" @click="mobileMenu = false"
                        class="block border-b border-white/10 py-3 text-sm
                            font-semibold text-slate-200 transition-colors
                            hover:text-amber-400">
                        {{ $title }}
                    </a>
                @endif
            @endforeach

            {{-- Tombol mobile --}}
            <div class="flex flex-col gap-3 pt-4">
                <a href="https://api.whatsapp.com/send?phone={{ $settings?->whatsapp_number ?? '6285343747243' }}&text=Halo%20BPVP%20Pangkep..."
                    target="_blank" rel="noopener noreferrer" @click="mobileMenu = false"
                    class="flex items-center justify-center rounded-xl bg-amber-400
                        py-2.5 text-xs font-bold text-slate-900
                        shadow-lg shadow-amber-500/10 transition-all
                        hover:bg-amber-500 active:scale-[0.98]">
                    <i class="fab fa-whatsapp mr-1.5 text-sm"></i>

                    Hubungi Kami
                </a>

                <a href="https://www.lapor.go.id/" target="_blank" rel="noopener noreferrer"
                    @click="mobileMenu = false"
                    class="flex items-center justify-center rounded-xl bg-red-600
                        py-2.5 shadow-lg shadow-red-600/20 transition-all
                        hover:bg-red-700 active:scale-[0.98]">
                    <img src="https://www.lapor.go.id/themes/lapor/assets/images/logo-white.png" alt="SP4N LAPOR!"
                        class="h-5 w-auto object-contain">
                </a>
            </div>
        </div>
    </div>
</header>
