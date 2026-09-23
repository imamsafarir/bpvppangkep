{{--
  PERBAIKAN UTAMA: Mengubah -mt-24 menjadi mt-16 agar posisi footer kembali normal
  dan tidak menabrak paksa tumpukan elemen di atasnya.
--}}
<footer id="kontak" class="text-slate-300 relative bg-transparent select-none mt-16 z-30" x-data="{ showScrollTop: false }"
    @scroll.window="showScrollTop = window.scrollY > 400">

    {{--
      PERBAIKAN KEDUA: Mengubah top-0 menjadi -top-[100px] agar hanya gelombang airnya saja
      yang meluncur keluar kontainer menimpa section halaman sebelumnya secara transparan.
    --}}
    <div class="w-full absolute -top-[100px] left-0 overflow-hidden leading-none pointer-events-none h-[115px] z-10">
        {{-- Sumbu Y pada viewBox diperlebar ke 150 agar memberi ruang ekstra di lantai bawah --}}
        <svg class="relative block w-[200%] h-full" viewBox="0 0 1200 150" preserveAspectRatio="none">

            {{--
              PERBAIKAN: Mengubah koordinat L1440,120 L0,120 menjadi L1440,200 L0,200
              Ini seperti memperpanjang "akar" warna biru ke bawah agar saat bergoyang tidak ada celah putih.
            --}}
            <path class="wave-layer-3"
                d="M0,40 C150,90 350,10 500,60 C650,110 850,30 1000,70 C1150,110 1300,40 1440,80 L1440,200 L0,200 Z"
                fill="#15406a"></path>

            <path class="wave-layer-2"
                d="M0,50 C180,20 320,90 540,40 C760,-10 920,80 1120,50 C1320,20 1380,70 1440,40 L1440,200 L0,200 Z"
                fill="#15406a"></path>

            <path class="wave-layer-1"
                d="M0,60 C200,30 400,80 600,50 C800,20 1000,70 1200,40 C1400,10 1420,60 1440,50 L1440,200 L0,200 Z"
                fill="#15406a"></path>

        </svg>
    </div>

    {{-- Mengubah pt-24 menjadi pt-12 karena gelombang sudah digeser ke luar batas atas --}}
    <div class="relative z-20 w-full pt-12 pb-8" style="background-color: #1b446f;">

        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-10 border-b border-white/10 pb-12">

            {{-- KOLOM 1: PROFIL & INSTANSI --}}
            <div class="md:col-span-5 space-y-4 text-left">
                <div class="flex items-center gap-3 select-none">
                    <img src="{{ $settings?->logo_path ? asset('storage/' . $settings->logo_path) : 'https://kemnaker.go.id/assets/images/logo.png' }}"
                        alt="Logo Footer" class="h-12 w-auto object-contain bg-white/10 p-1.5 rounded-xl">
                    <div class="leading-tight">
                        <span
                            class="font-black text-white text-base block tracking-tight">{{ \App\Modules\Website\Models\WebsiteSetting::first()?->website_name ?? 'BPVP PANGKEP' }}</span>
                        <span class="text-[9px] font-bold block uppercase tracking-wider text-slate-300">KEMNAKER
                            RI</span>
                    </div>
                </div>
                <div class="text-xs sm:text-sm leading-relaxed font-normal text-slate-300/90 break-words">
                    <strong class="text-white block font-extrabold tracking-tight mb-1">
                        Balai Pelatihan Vokasi dan Produktivitas Pangkajene dan Kepulauan
                    </strong>
                    <div class="flex items-start gap-2 mt-2">
                        <i class="fas fa-map-marker-alt text-amber-400 mt-1 flex-shrink-0 w-4"></i>
                        <span>{{ $settings?->address ?? 'Jl. Poros Makassar - Parepare KM. 83, Mandalle, Kab. Pangkajene dan Kepulauan, Sulawesi Selatan' }}</span>
                    </div>
                </div>
            </div>

            {{-- KOLOM 2: LINK TAUTAN PINTAS --}}
            <div class="md:col-span-3 space-y-3 md:pl-6 text-left">
                <h4 class="text-white font-extrabold text-sm uppercase tracking-wider border-l-2 border-amber-400 pl-2">
                    Tautan Pintas</h4>
                <ul class="text-xs sm:text-sm space-y-2 font-semibold">
                    <li><a href="/" class="hover:text-amber-400 transition-colors flex items-center gap-1.5">â€º
                            Beranda</a></li>
                    <li><a href="{{ route('profil.tentang-kami') }}"
                            class="hover:text-amber-400 transition-colors flex items-center gap-1.5">â€º Profil Balai</a>
                    </li>
                    <li><a href="{{ route('informasi.kejuruan') }}"
                            class="hover:text-amber-400 transition-colors flex items-center gap-1.5">â€º Program
                            Kejuruan</a></li>
                    <li><a href="{{ route('berita.index') }}"
                            class="hover:text-amber-400 transition-colors flex items-center gap-1.5">â€º Kabar Berita</a>
                    </li>
                    <li><a href="{{ route('jdih.index') }}"
                            class="hover:text-amber-400 transition-colors flex items-center gap-1.5">â€º JDIH Hukum</a>
                    </li>
                </ul>
            </div>

            {{-- KOLOM 3: KONTAK & MEDSOS --}}
            <div class="md:col-span-4 space-y-3 w-full text-left">
                <h4 class="text-white font-extrabold text-sm uppercase tracking-wider border-l-2 border-amber-400 pl-2">
                    Kontak Hubung</h4>
                <ul class="text-xs sm:text-sm space-y-2.5 font-medium">
                    @if ($settings?->email)
                        <li class="flex items-center gap-2">
                            <div
                                class="w-7 h-7 bg-white/5 rounded-lg flex items-center justify-center text-indigo-300 flex-shrink-0">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <span class="truncate">{{ $settings->email }}</span>
                        </li>
                    @endif
                    @if ($settings?->phone_number)
                        <li class="flex items-center gap-2">
                            <div
                                class="w-7 h-7 bg-white/5 rounded-lg flex items-center justify-center text-emerald-400 flex-shrink-0">
                                <i class="fas fa-phone"></i>
                            </div>
                            <span>{{ $settings->phone_number }}</span>
                        </li>
                    @endif
                </ul>

                <div class="pt-2 flex flex-wrap gap-2">
                    @if ($settings?->instagram_url)
                        <a href="{{ $settings->instagram_url }}" target="_blank"
                            class="w-8 h-8 rounded-xl bg-white/5 hover:bg-pink-600 hover:text-white text-pink-400 flex items-center justify-center transition-all shadow-2xs"><i
                                class="fab fa-instagram"></i></a>
                    @endif
                    @if ($settings?->tiktok_url)
                        <a href="{{ $settings->tiktok_url }}" target="_blank"
                            class="w-8 h-8 rounded-xl bg-white/5 hover:bg-black hover:text-white text-slate-200 flex items-center justify-center transition-all shadow-2xs"><i
                                class="fab fa-tiktok"></i></a>
                    @endif
                    @if ($settings?->facebook_url)
                        <a href="{{ $settings->facebook_url }}" target="_blank"
                            class="w-8 h-8 rounded-xl bg-white/5 hover:bg-blue-600 hover:text-white text-blue-400 flex items-center justify-center transition-all shadow-2xs"><i
                                class="fab fa-facebook-f"></i></a>
                    @endif
                    @if ($settings?->youtube_url)
                        <a href="{{ $settings->youtube_url }}" target="_blank"
                            class="w-8 h-8 rounded-xl bg-white/5 hover:bg-red-600 hover:text-white text-red-400 flex items-center justify-center transition-all shadow-2xs"><i
                                class="fab fa-youtube"></i></a>
                    @endif
                </div>
            </div>

        </div>

        {{-- BAGIAN BAWAH: COPYRIGHT --}}
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-[11px] font-medium text-slate-300/70">
            <p>&copy; 2026 Balai Pelatihan Vokasi dan Produktivitas {{ $settings?->website_name ?? 'BPVP Pangkep' }}.
                All Rights Reserved.</p>
            <p
                class="tracking-widest uppercase text-[10px] text-white font-bold bg-white/5 px-3 py-1 rounded-md border border-white/5">
                KEMNAKER RI</p>
        </div>

    </div>

    {{-- INTERAKTIF BACK TO TOP BUTTON --}}
    <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })" x-show="showScrollTop"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-6 right-6 w-10 h-10 bg-amber-400 hover:bg-amber-500 text-slate-900 rounded-xl flex items-center justify-center shadow-lg hover:shadow-amber-400/20 active:scale-95 transition-all z-50 cursor-pointer focus:outline-hidden"
        title="Kembali ke Atas" style="display: none;">
        <i class="fas fa-chevron-up text-sm font-black"></i>
    </button>

</footer>

