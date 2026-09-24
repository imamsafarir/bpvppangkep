const CACHE_NAME = "bpvp-client-cache-v3";
const CORE_STATIC_URLS = [
    "/",
    "/manifest.json",
    "/favicon.ico",
    "/favicon.svg",
    "/default-favicon.png",
    "/icon-192.png",
    "/icon-512.png",
];

// Install Event: Simpan aset utama ke cache lokal browser masing-masing pengunjung
self.addEventListener("install", (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(CORE_STATIC_URLS).catch(() => {});
        }),
    );
});

// Activate Event: Bersihkan cache versi lama saat service worker baru aktif
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME) {
                            return caches.delete(cacheName);
                        }
                    }),
                );
            })
            .then(() => self.clients.claim()),
    );
});

// Fetch Event: Strategi Caching Pintar per pengunjung
self.addEventListener("fetch", (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Jangan pernah meng-cache request selain GET, atau route admin, login, dan livewire
    if (
        request.method !== "GET" ||
        url.pathname.startsWith("/admin") ||
        url.pathname.startsWith("/livewire") ||
        url.pathname.startsWith("/login") ||
        url.pathname.startsWith("/logout")
    ) {
        return;
    }

    // 1. Aset Statis (CSS, JS, Fonts, Images, SVG): Cache-First Strategy
    // Mengambil langsung dari cache browser lokal secara instan (0 ms), lalu fetch jika belum ada
    const isStaticAsset =
        url.pathname.startsWith("/build/") ||
        url.pathname.startsWith("/css/") ||
        url.pathname.startsWith("/js/") ||
        url.pathname.startsWith("/storage/") ||
        url.pathname.match(
            /\.(css|js|woff2?|ttf|eot|svg|png|jpe?g|webp|gif|ico)$/i,
        ) ||
        url.hostname.includes("fonts.googleapis.com") ||
        url.hostname.includes("fonts.gstatic.com") ||
        url.hostname.includes("cdnjs.cloudflare.com") ||
        url.hostname.includes("cdn.jsdelivr.net");

    if (isStaticAsset) {
        event.respondWith(
            caches.open(CACHE_NAME).then((cache) => {
                return cache.match(request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    return fetch(request)
                        .then((networkResponse) => {
                            if (
                                networkResponse &&
                                networkResponse.status === 200
                            ) {
                                cache.put(request, networkResponse.clone());
                            }
                            return networkResponse;
                        })
                        .catch(() => cachedResponse);
                });
            }),
        );
        return;
    }

    // 2. Navigasi Halaman HTML Website: Network-First dengan Fallback Cepat ke Cache
    if (request.mode === "navigate") {
        event.respondWith(
            caches.open(CACHE_NAME).then((cache) => {
                return fetch(request)
                    .then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            cache.put(request, networkResponse.clone());
                        }
                        return networkResponse;
                    })
                    .catch(() => {
                        // Jika koneksi lambat atau offline, sajikan langsung dari cache browser lokal pengunjung
                        return cache.match(request).then((cachedResponse) => {
                            return cachedResponse || cache.match("/");
                        });
                    });
            }),
        );
        return;
    }
});
