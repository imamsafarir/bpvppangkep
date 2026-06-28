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

    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="bg-slate-50 text-slate-800" x-data="{ isScrolled: false, mobileMenu: false }" @scroll.window="isScrolled = window.scrollY > 40">

    
    <?php echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/layouts/app.blade.php ENDPATH**/ ?>