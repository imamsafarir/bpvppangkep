<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Title Dinamis: Menggunakan @yield agar tiap halaman bisa mengganti judulnya --}}
    <title>@yield('title', $settings?->website_name ?? 'BPVP Pangkep')</title>

    {{-- Favicon Global --}}
    <link rel="icon" type="image/png"
        href="{{ $settings?->favicon_path ? asset('storage/' . $settings->favicon_path) : asset('default-favicon.png') }}">

    {{-- Asset Global --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Jalur animasi ombak mengalir cair */
        @keyframes wave-drift-right {
            0% {
                transform: translate3d(0, 0, 0) scaleY(1);
            }

            50% {
                transform: translate3d(-25%, 0, 0) scaleY(0.85) skewY(1deg);
            }

            100% {
                transform: translate3d(-50%, 0, 0) scaleY(1);
            }
        }

        @keyframes wave-drift-left {
            0% {
                transform: translate3d(-50%, 0, 0) scaleY(1);
            }

            50% {
                transform: translate3d(-25%, 0, 0) scaleY(0.9) skewY(-1deg);
            }

            100% {
                transform: translate3d(0, 0, 0) scaleY(1);
            }
        }

        /* Pemanggilan class untuk 3 lapisan gelombang */
        .wave-layer-1 {
            animation: wave-drift-right 12s cubic-bezier(0.4, 0.45, 0.55, 0.6) infinite;
            opacity: 0.95;
        }

        .wave-layer-2 {
            animation: wave-drift-left 8s cubic-bezier(0.35, 0.45, 0.65, 0.7) infinite;
            opacity: 0.4;
        }

        .wave-layer-3 {
            animation: wave-drift-right 22s cubic-bezier(0.5, 0.5, 0.5, 0.5) infinite;
            opacity: 0.2;
        }
    </style>

    {{-- Wadah CSS Tambahan jika suatu halaman butuh style unik --}}
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-800" x-data="{ isScrolled: false, mobileMenu: false }" @scroll.window="isScrolled = window.scrollY > 40">

    {{-- Memanggil Navbar Otomatis di Semua Halaman --}}
    @include('layouts.navbar')

    {{-- Tempat Menyisipkan Konten Unik Masing-masing Halaman --}}
    <main>
        @yield('content')
    </main>

    {{-- Memanggil Footer Otomatis di Semua Halaman --}}
    @include('layouts.footer')

    {{-- Wadah Script Tambahan jika suatu halaman butuh JS unik --}}
    @stack('scripts')
</body>

</html>
