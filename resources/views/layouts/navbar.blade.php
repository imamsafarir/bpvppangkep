<style>
    .animate-marquee {
        display: inline-block;
        padding-left: 100%;
        animation: marqueeAnimation 100s linear infinite;
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

{{-- Integrasi penuh data menu dengan state penutup otomatis di luar area click --}}
<header id="navbar-container" class="fixed top-0 left-0 w-full z-40 transition-all duration-500" x-data="{ openMenu: null }">

    @if ($settings?->is_running_text_active && $settings->running_text_content)
        <div class="text-white py-2.5 overflow-hidden relative w-full shadow-sm border-b border-white/10 z-50 select-none"
            style="background-color: #1b446f;">
            <div class="flex whitespace-nowrap animate-marquee font-medium text-xs tracking-wide uppercase">
                <span class="mx-4 text-amber-300 font-bold flex-shrink-0">⚠️ INFO TERKINI:</span>
                <span class="pl-2">{{ $settings->running_text_content }}</span>
            </div>
        </div>
    @endif

    {{--
      PERBAIKAN SINKRONISASI & AKSEN ORANYE:
      Ketika di-scroll, border-bottom berubah menjadi warna amber-400/30 tipis yang sangat elegan.
    --}}
    <div id="navbar-main" class="w-full transition-all duration-500 border-b flex items-center py-4"
        @if (Route::is('home')) :class="isScrolled ? 'shadow-md border-amber-400/30 bg-[#15406a]' : 'bg-transparent border-transparent'"
        @else
            class="shadow-md border-amber-400/30"
            style="background-color: #15406a;" @endif>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center w-full">

            <div class="flex items-center gap-3 select-none text-white">
                <img src="{{ $settings && $settings->logo_path
                    ? asset('storage/' . $settings->logo_path)
                    : asset('images/default-logo.png') }}"
                    alt="" class="h-10 object-contain" />
                <div class="leading-tight">
                    <span
                        class="font-black text-lg block tracking-tight drop-shadow-md">{{ \App\Models\WebsiteSetting::first()?->website_name ?? 'BPVP PANGKEP' }}</span>
                    <span
                        class="text-[9px] font-bold block uppercase tracking-wider text-amber-400 drop-shadow-xs">KEMNAKER
                        RI</span>
                </div>
            </div>

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
                    'JDIH' => ['type' => 'link', 'url' => route('jdih.index')],
                ];
            @endphp

            <nav class="hidden lg:flex items-center gap-5 font-semibold text-sm text-white">
                <a href="/"
                    class="text-white/90 hover:text-amber-400 transition-colors drop-shadow-xs">Beranda</a>

                @foreach ($navigationMenu as $title => $menu)
                    @if ($menu['type'] === 'dropdown')
                        <div class="relative py-2" @mouseenter="openMenu = '{{ $menu['key'] }}'"
                            @mouseleave="openMenu = null" @click.away="openMenu = null">

                            <button
                                @click="openMenu = (openMenu === '{{ $menu['key'] }}' ? null : '{{ $menu['key'] }}')"
                                class="flex items-center gap-1 text-white/90 hover:text-amber-400 transition-colors cursor-pointer drop-shadow-xs focus:outline-hidden">
                                <span>{{ $title }}</span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                    :class="openMenu === '{{ $menu['key'] }}' ? 'rotate-180 text-amber-400' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="openMenu === '{{ $menu['key'] }}'"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute left-0 mt-2 {{ $menu['width'] }} bg-white rounded-xl shadow-xl border border-slate-100 py-2 text-slate-800 z-50">
                                @foreach ($menu['links'] as $label => $url)
                                    <a href="{{ $url }}"
                                        class="block px-4 py-2 hover:bg-amber-50 hover:text-blue-900 border-l-2 border-transparent hover:border-amber-500 font-semibold transition-all text-xs sm:text-sm">{{ $label }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $menu['url'] }}"
                            class="text-white/90 hover:text-amber-400 transition-colors drop-shadow-xs">{{ $title }}</a>
                    @endif
                @endforeach

                <a href="https://api.whatsapp.com/send?phone={{ $settings?->whatsapp_number ?? '6285343747243' }}&text=Halo%20BPVP%20Pangkep..."
                    target="_blank"
                    class="px-4 py-2 rounded-xl text-xs font-black transition-all active:scale-95 shadow-md bg-amber-400 hover:bg-amber-500 text-slate-900 shadow-amber-500/10">
                    Hubungi Kami
                </a>
            </nav>

            <button @click="mobileMenu = !mobileMenu"
                class="lg:hidden p-2 text-xl focus:outline-hidden text-white cursor-pointer">
                <i class="fas" :class="mobileMenu ? 'fa-times text-amber-400' : 'fa-bars'"></i>
            </button>
        </div>
    </div>

    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        class="lg:hidden bg-[#15406a] border-t border-amber-400/30 px-5 pt-2 pb-6 space-y-1 shadow-xl absolute left-0 w-full text-white z-50"
        style="display: none;" x-data="{ activeTab: null }">

        <a href="/" @click="mobileMenu = false"
            class="block font-bold text-amber-400 py-3 border-b border-white/10 text-sm">Beranda</a>

        @foreach ($navigationMenu as $title => $menu)
            @if ($menu['type'] === 'dropdown')
                <div class="border-b border-white/10">
                    <button @click="activeTab = (activeTab === '{{ $menu['key'] }}' ? null : '{{ $menu['key'] }}')"
                        class="w-full flex justify-between items-center font-semibold text-slate-200 py-3 text-sm focus:outline-hidden cursor-pointer"
                        :class="activeTab === '{{ $menu['key'] }}' ? 'text-amber-400' : ''">
                        <span>{{ $title }}</span>
                        <i class="fas text-xs transition-transform duration-200"
                            :class="activeTab === '{{ $menu['key'] }}' ? 'fa-chevron-up text-amber-400' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="activeTab === '{{ $menu['key'] }}'" x-transition
                        class="pl-4 pb-2 space-y-2 text-xs font-medium text-slate-300 border-l border-amber-400/40 ml-1">
                        @foreach ($menu['links'] as $label => $url)
                            <a href="{{ $url }}" @click="mobileMenu = false"
                                class="block py-1 hover:text-amber-400 transition-colors">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
            @else
                <a href="{{ $menu['url'] }}" @click="mobileMenu = false"
                    class="block font-semibold text-slate-200 hover:text-amber-400 py-3 border-b border-white/10 text-sm">{{ $title }}</a>
            @endif
        @endforeach

        <div class="pt-4">
            <a href="https://api.whatsapp.com/send?phone={{ $settings?->whatsapp_number ?? '6285343747243' }}&text=Halo%20BPVP%20Pangkep..."
                target="_blank" @click="mobileMenu = false"
                class="block text-center bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold py-2.5 rounded-xl text-xs shadow-lg shadow-amber-500/10 active:scale-98 transition-all">
                <i class="fab fa-whatsapp mr-1 text-sm"></i> Hubungi Kami
            </a>
        </div>
    </div>
</header>
