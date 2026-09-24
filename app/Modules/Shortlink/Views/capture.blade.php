<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shortlink->display_title }} - BPVP Pangkep</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-slate-50 via-indigo-50/40 to-slate-100 min-h-screen flex items-center justify-center p-4">

    <div
        class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-indigo-100/50 border border-slate-100 overflow-hidden">

        <!-- Header Card -->
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-8 text-center text-white relative">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-md mb-3 ring-4 ring-white/20">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                    </path>
                </svg>
            </div>
            <h1 class="text-xl font-bold tracking-tight">{{ $shortlink->display_title }}</h1>
        </div>

        <!-- Form Body -->
        <div class="p-6 md:p-8">
            <p class="text-xs text-slate-500 mb-6 text-center leading-relaxed">
                {!! nl2br(e($shortlink->display_description)) !!}
            </p>

            @if (!empty($errors) && $errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('shortlink.submit', $shortlink->code) }}" method="POST" class="space-y-4">
                @csrf

                @if (in_array('nama', $fields))
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama
                            Lengkap <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            <input type="text" name="nama" value="{{ old('nama') }}" required
                                placeholder="Masukkan nama Anda"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 text-sm text-slate-800 transition outline-none">
                        </div>
                    </div>
                @endif

                @if (in_array('whatsapp', $fields))
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nomor
                            WhatsApp <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                            </span>
                            <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}" required
                                placeholder="Contoh: 08123456789"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 text-sm text-slate-800 transition outline-none">
                        </div>
                    </div>
                @endif

                @if (in_array('email', $fields))
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat
                            Email <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="nama@email.com"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 text-sm text-slate-800 transition outline-none">
                        </div>
                    </div>
                @endif

                <button type="submit" id="btn-submit"
                    class="w-full py-3.5 px-4 rounded-xl font-semibold text-sm transition-all flex items-center justify-center gap-2 group mt-2 cursor-not-allowed bg-slate-200 text-slate-400 shadow-none"
                    disabled>
                    <span>{{ $shortlink->display_button_text }}</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>
        </div>

        <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 text-center">
            <p class="text-[11px] text-slate-400 font-medium">BPVP Pangkep &bull; Sistem Layanan Terintegrasi</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const btnSubmit = document.getElementById('btn-submit');
            const requiredInputs = form.querySelectorAll('input[required]');

            function checkFormValidity() {
                let isValid = true;

                requiredInputs.forEach(function(input) {
                    if (!input.value.trim()) {
                        isValid = false;
                    }
                    if (input.type === 'email' && input.value.trim()) {
                        // Cek format email dasar
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(input.value.trim())) {
                            isValid = false;
                        }
                    }
                });

                if (isValid) {
                    btnSubmit.disabled = false;
                    btnSubmit.classList.remove('cursor-not-allowed', 'bg-slate-200', 'text-slate-400',
                        'shadow-none');
                    btnSubmit.classList.add('cursor-pointer', 'bg-indigo-600', 'hover:bg-indigo-700',
                        'active:bg-indigo-800', 'text-white', 'shadow-lg', 'shadow-indigo-200');
                } else {
                    btnSubmit.disabled = true;
                    btnSubmit.classList.add('cursor-not-allowed', 'bg-slate-200', 'text-slate-400', 'shadow-none');
                    btnSubmit.classList.remove('cursor-pointer', 'bg-indigo-600', 'hover:bg-indigo-700',
                        'active:bg-indigo-800', 'text-white', 'shadow-lg', 'shadow-indigo-200');
                }
            }

            requiredInputs.forEach(function(input) {
                input.addEventListener('input', checkFormValidity);
                input.addEventListener('change', checkFormValidity);
            });

            // Jalankan cek pertama saat halaman selesai dimuat (misal jika ada old value dari session)
            checkFormValidity();
        });
    </script>
</body>

</html>
