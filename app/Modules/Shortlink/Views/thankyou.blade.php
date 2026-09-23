<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="2;url={{ $destinationUrl }}">
    <title>Terima Kasih!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @keyframes scaleUp {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-card {
            animation: scaleUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-slate-50 via-emerald-50/40 to-slate-100 min-h-screen flex items-center justify-center p-4">

    <div
        class="w-full max-w-sm bg-white rounded-3xl shadow-xl shadow-emerald-100/50 border border-slate-100 overflow-hidden text-center p-8 animate-card">

        <!-- Icon Sukses -->
        <div
            class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 mb-5 ring-8 ring-emerald-50">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Terima Kasih!</h1>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">
            Data Anda telah kami terima dengan baik. Menghubungkan Anda ke tautan tujuan...
        </p>

        <!-- Loader Spinner -->
        <div class="mt-6 flex items-center justify-center gap-2 text-indigo-600 font-semibold text-xs">
            <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Mengarahkan secara otomatis...</span>
        </div>

        <div class="mt-6 pt-5 border-t border-slate-100">
            <p class="text-xs text-slate-400">
                Jika tidak otomatis teralihkan, <a href="{{ $destinationUrl }}"
                    class="text-indigo-600 font-semibold hover:underline">klik di sini</a>.
            </p>
        </div>
    </div>

    <script>
        // Redirect cadangan via JS
        setTimeout(function() {
            window.location.href = "{{ $destinationUrl }}";
        }, 1800);
    </script>
</body>

</html>
