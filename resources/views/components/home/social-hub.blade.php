@props(['settings'])
<section id="multi-browser-hub"
    class="bg-gradient-to-b from-white to-slate-50/80 py-16 border-t border-slate-100 overflow-hidden">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="text-center mb-10">
            <span
                class="text-xs font-bold text-amber-500 uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-md border border-amber-200/50 inline-block mb-1">
                <i class="fas fa-desktop mr-1"></i> Live Desktop Hub
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Multi-Browser Live Feed
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Arahkan kursor untuk menghentikan carousel, atau seret slider manual di bawah untuk mengontrol
                halaman.
            </p>
        </div>

        {{-- CAROUSEL WRAPPER DENGAN SINKRONISASI SLIDER BAR MANUAL --}}
        <div class="w-full space-y-6" x-data="{
            scrollSpeed: 0.7,
            currentSpeed: 0.7,
            scrollPercent: 0,
            isDragging: false,
            init() {
                const el = this.$refs.track;
                const loop = () => {
                    // Hanya berjalan otomatis jika slider TIDAK sedang digeser manual
                    if (!this.isDragging) {
                        el.scrollLeft += this.currentSpeed;
                        if (el.scrollLeft >= el.scrollWidth / 2) {
                            el.scrollLeft = 0;
                        }
                    }
                    requestAnimationFrame(loop);
                };
                requestAnimationFrame(loop);

                // Update tracker persentase bar di bawah secara realtime saat carousel bergulir otomatis
                el.addEventListener('scroll', () => {
                    if (this.isDragging) return;
                    const maxScroll = el.scrollWidth - el.clientWidth;
                    if (maxScroll > 0) {
                        this.scrollPercent = (el.scrollLeft / maxScroll) * 100;
                    }
                });

                // HOVER STOP: Mengontrol kecepatan laju putaran track secara global
                el.addEventListener('mouseenter', () => { if (!this.isDragging) this.currentSpeed = 0; });
                el.addEventListener('mouseleave', () => { if (!this.isDragging) this.currentSpeed = this.scrollSpeed; });
                el.addEventListener('touchstart', () => { if (!this.isDragging) this.currentSpeed = 0; });
                el.addEventListener('touchend', () => { if (!this.isDragging) this.currentSpeed = this.scrollSpeed; });
            }
        }" x-init="init()">

            {{-- TRACK BARIS FRAME BROWSER --}}
            <div x-ref="track" class="flex gap-8 py-6 overflow-x-auto scrollbar-none select-none"
                style="white-space: nowrap; -ms-overflow-style: none; scrollbar-width: none;">

                @php
                    $fbUrl = $settings?->facebook_url ?? 'https://www.facebook.com/bpvppangkep';
                    $igUrl = $settings?->instagram_url ?? 'https://www.instagram.com/bpvppangkep';
                    $ytUrl = $settings?->youtube_url ?? 'https://www.youtube.com/channel/UCxxxx';
                    $ttUrl = $settings?->tiktok_url ?? 'https://www.tiktok.com/@bpvp.pangkep';

                    $items = [
                        [
                            'name' => 'Instagram Official',
                            'icon' => 'fab fa-instagram text-pink-500',
                            'url' => 'instagram.com/bpvppangkep',
                            'type' => 'instagram',
                            'border' => 'border-pink-500',
                            'src' => str_contains($igUrl, '/embed') ? $igUrl : rtrim($igUrl, '/') . '/embed',
                        ],
                        [
                            'name' => 'TikTok Feed Stream',
                            'icon' => 'fab fa-tiktok text-slate-300',
                            'url' => 'tiktok.com/@bpvp.pangkep',
                            'type' => 'tiktok',
                            'border' => 'border-amber-400',
                            'src' => str_contains($ttUrl, 'embed')
                                ? $ttUrl
                                : str_replace('com/@', 'com/embed/@', $ttUrl),
                        ],
                        [
                            'name' => 'Facebook Timeline',
                            'icon' => 'fab fa-facebook text-blue-500',
                            'url' => 'facebook.com/bpvppangkep',
                            'type' => 'facebook',
                            'border' => 'border-blue-500',
                            'src' =>
                                'https://www.facebook.com/plugins/page.php?href=' .
                                urlencode($fbUrl) .
                                '&tabs=timeline&width=500&height=490&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=false',
                        ],
                        [
                            'name' => 'YouTube Channel',
                            'icon' => 'fab fa-youtube text-red-500',
                            'url' => 'youtube.com/bpvppangkep',
                            'type' => 'youtube',
                            'border' => 'border-red-500',
                            'src' => str_contains($ytUrl, 'embed')
                                ? $ytUrl
                                : str_replace('youtube.com/channel/', 'youtube.com/embed/videoseries?list=', $ytUrl),
                        ],
                    ];

                    $loopItems = array_merge($items, $items);
                @endphp

                @foreach ($loopItems as $item)
                    {{-- CARD CONTAINER --}}
                    <div
                        class="inline-block w-[420px] sm:w-[500px] h-[560px] bg-slate-950 rounded-2xl shadow-xl border border-slate-800 overflow-hidden flex-shrink-0 flex flex-col transition-all duration-300">

                        {{-- BROWSER BAR HEADER --}}
                        <div
                            class="bg-slate-900 px-4 pt-3 pb-2 border-t-2 {{ $item['border'] }} border-b border-slate-800 flex-shrink-0">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-3">
                                    <div class="flex gap-1.5">
                                        <span class="w-2 h-2 bg-red-500 rounded-full block"></span>
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full block"></span>
                                        <span class="w-2 h-2 bg-green-500 rounded-full block"></span>
                                    </div>
                                    <span
                                        class="text-[11px] text-slate-300 font-bold truncate flex items-center gap-1.5">
                                        <i class="{{ $item['icon'] }} text-xs"></i> {{ $item['name'] }}
                                    </span>
                                </div>
                                <i class="fas fa-redo-alt text-slate-600 text-[9px]"></i>
                            </div>
                            <div
                                class="w-full bg-slate-950 text-slate-500 text-[9px] font-mono px-2.5 py-1 rounded border border-slate-800/80 truncate text-left">
                                🔒 https://{{ $item['url'] }}
                            </div>
                        </div>

                        {{-- CONTENT VIEWPORT SCREEN --}}
                        <div class="flex-1 w-full bg-white overflow-hidden relative" style="height: calc(100% - 75px);">
                            @if ($item['src'] && !str_contains($item['src'], 'UCxxxx'))
                                <iframe src="{{ $item['src'] }}" class="w-full border-none bg-white block"
                                    style="width: 100%; height: 100% !important; min-height: 480px; max-height: 100%; margin: 0; padding: 0;"
                                    loading="lazy"
                                    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>
                            @else
                                <div
                                    class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 text-xs p-6 text-center bg-slate-900">
                                    <i class="{{ $item['icon'] }} text-3xl mb-2 text-white/20"></i>
                                    <span class="text-white/60 font-bold block">Kanal Belum Terhubung</span>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach

            </div>

            {{-- 🎛️ NEW COMPONENT: CONTROL INPUT RANGE SLIDER BAR MANUALLY --}}
            <div class="max-w-xs mx-auto pt-2 flex items-center gap-3 justify-center select-none">
                <i class="fas fa-chevron-left text-slate-400 text-[10px]"></i>

                <div class="relative w-full h-1 bg-slate-200 rounded-full">
                    <div class="absolute top-0 left-0 h-full bg-amber-400 rounded-full transition-all duration-75"
                        :style="'width: ' + scrollPercent + '%'"></div>

                    <input type="range" min="0" max="100" x-model="scrollPercent"
                        @mousedown="isDragging = true" @touchstart="isDragging = true"
                        @input="$refs.track.scrollLeft = (scrollPercent / 100) * ($refs.track.scrollWidth - $refs.track.clientWidth)"
                        @change="isDragging = false; currentSpeed = scrollSpeed"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30">
                </div>

                <i class="fas fa-chevron-right text-slate-400 text-[10px]"></i>
            </div>

        </div>
    </div>
</section>
