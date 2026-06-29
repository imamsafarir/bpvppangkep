<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <title><?php echo $__env->yieldContent('title', $settings?->website_name ?? 'BPVP Pangkep'); ?></title>

    
    <link rel="icon" type="image/png"
        href="<?php echo e($settings?->favicon_path ? asset('storage/' . $settings->favicon_path) : asset('default-favicon.png')); ?>">

    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/open-dyslexic@1.0.3/open-dyslexic-regular.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/open-dyslexic@1.0.3/index.min.js"></script>

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

        /* =========================================================
           🟢 PERBAIKAN TOTAL: CSS ACCESSIBILITY YANG AMAN (ANTI-BLANK)
           ========================================================= */
        [x-cloak] {
            display: none !important;
        }

        html.ax-text-large {
            font-size: 115% !important;
        }

        html.ax-text-xlarge {
            font-size: 130% !important;
        }

        /* Menggunakan filter hardware agar layout tidak hilang/pecah */
        html.ax-grayscale {
            filter: grayscale(100%) !important;
        }

        html.ax-sepia {
            filter: sepia(100%) !important;
        }

        html.ax-invert {
            filter: invert(100%) !important;
        }

        html.ax-high-contrast {
            filter: contrast(180%) brightness(100%) !important;
        }

        /* =========================================================
           🟢 PERBAIKAN FINAL: MENYESUAIKAN NAMA FONT SESUAI DOKUMENTASI
           ========================================================= */

        /* 1. TERAPKAN FONT MENGGUNAKAN NAMA 'OpenDyslexicRegular' */
        html.ax-dyslexia-font,
        html.ax-dyslexia-font body,
        html.ax-dyslexia-font p,
        html.ax-dyslexia-font span,
        html.ax-dyslexia-font a,
        html.ax-dyslexia-font h1,
        html.ax-dyslexia-font h2,
        html.ax-dyslexia-font h3,
        html.ax-dyslexia-font h4,
        html.ax-dyslexia-font h5,
        html.ax-dyslexia-font h6,
        html.ax-dyslexia-font li,
        html.ax-dyslexia-font button {
            /* 🟢 Menggunakan OpenDyslexicRegular sesuai manifest CDN kamu */
            font-family: 'OpenDyslexicRegular', 'OpenDyslexic', sans-serif !important;
            letter-spacing: 0.06em !important;
            word-spacing: 0.12em !important;
        }

        /* 2. PROTEKSI UTUH: Benteng pertahanan agar ikon Font Awesome tidak hilang */
        html.ax-dyslexia-font .fa,
        html.ax-dyslexia-font .fas,
        html.ax-dyslexia-font .far,
        html.ax-dyslexia-font .fab,
        html.ax-dyslexia-font [class*="fa-"],
        html.ax-dyslexia-font [class*="fa-"]::before,
        html.ax-dyslexia-font [class*="fa-"]::after {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", "Poppins", sans-serif !important;
            font-weight: 900 !important;
        }

        html.ax-force-underline a {
            text-decoration: underline !important;
            text-decoration-color: #2563eb !important;
            text-decoration-thickness: 2px !important;
        }

        html.ax-big-cursor,
        html.ax-big-cursor * {
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 24 24' fill='%232563eb' stroke='white' stroke-width='1.5'%3E%3Cpath d='M4.5 3v15.25l3.96-3.83 2.58 5.83 2.67-1.17-2.58-5.83 5.37-.08L4.5 3z'/%3E%3C/svg%3E"), auto !important;
        }

        /* =========================================================
           🟢 SUNTIKAN FILTER WARNA KHUSUS BUTA WARNA (DALTONISME)
           ========================================================= */
        html.ax-protanopia {
            filter: url('#ax-filter-protanopia') !important;
        }

        html.ax-deuteranopia {
            filter: url('#ax-filter-deuteranopia') !important;
        }

        html.ax-tritanopia {
            filter: url('#ax-filter-tritanopia') !important;
        }

        /* Load font disleksia premium internasional */
        @import url('https://cdn.jsdelivr.net/npm/opendyslexic@1.0.3/popup.min.css');

        html.ax-dyslexia-font,
        html.ax-dyslexia-font * {
            font-family: 'OpenDyslexic', sans-serif !important;
            letter-spacing: 0.06em !important;
            word-spacing: 0.12em !important;
        }
    </style>

    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="bg-slate-50 text-slate-800" x-data="{ isScrolled: false, mobileMenu: false }" @scroll.window="isScrolled = window.scrollY > 40">

    
    <?php echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('components.accessibility-widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/layouts/app.blade.php ENDPATH**/ ?>