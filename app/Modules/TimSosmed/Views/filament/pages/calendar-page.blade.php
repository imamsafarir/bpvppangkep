<x-filament-panels::page>
    {{-- ================= TAILWIND CDN (MEMAKSA WARNA MUNCUL) ================= --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            corePlugins: {
                preflight: false, // Wajib false agar tidak merusak form Filament
            }
        }
    </script>

    {{-- ================= PANEL LEGENDA & PETUNJUK ================= --}}
    <div
        class="mb-4 space-y-4 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-gray-700 shadow-sm dark:border-yellow-900/50 dark:bg-yellow-900/20 dark:text-gray-300">

        {{-- Petunjuk Klik --}}
        <div class="flex flex-col gap-2 sm:flex-row sm:gap-6 font-medium">
            <span class="flex items-center gap-2">📅 <span>Klik tanggal kosong untuk <strong
                        class="text-yellow-800 dark:text-yellow-400">Buat Konten Baru</strong></span></span>
            <span class="flex items-center gap-2">📌 <span>Klik sticky note untuk <strong
                        class="text-yellow-800 dark:text-yellow-400">Mengerjakan Konten</strong></span></span>
        </div>

        <hr class="border-yellow-200 dark:border-yellow-800/50">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Keterangan Ikon Tim --}}
            <div>
                <strong class="block mb-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Arti Ikon
                    Tim:</strong>
                <div class="flex flex-wrap gap-4 text-xs font-semibold">
                    <span class="flex items-center gap-1 rounded-md bg-white/60 dark:bg-black/20 px-2 py-1 shadow-sm">
                        <span>📝</span> Planner
                    </span>
                    <span class="flex items-center gap-1 rounded-md bg-white/60 dark:bg-black/20 px-2 py-1 shadow-sm">
                        <span>🎬</span> Editor
                    </span>
                    <span class="flex items-center gap-1 rounded-md bg-white/60 dark:bg-black/20 px-2 py-1 shadow-sm">
                        <span>🌐</span> Admin
                    </span>
                </div>
            </div>

            {{-- Keterangan Warna Status --}}
            <div>
                <strong class="block mb-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Arti Warna
                    Status:</strong>
                <div class="flex flex-wrap gap-3 text-xs font-semibold">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full shadow-sm bg-gray-500"></span> Draft
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full shadow-sm bg-red-500"></span> Menunggu Editor
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full shadow-sm bg-blue-500"></span> Siap Publish
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full shadow-sm bg-green-500"></span> Publish
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Script FullCalendar --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

    <style>
        /* Mengecilkan header kalender */
        .fc .fc-col-header-cell-cushion {
            font-size: 0.875rem;
            padding: 4px;
        }

        /* Modifikasi base event agar overflow terlihat (untuk menampilkan pin) */
        .fc-event {
            background: transparent !important;
            border: none !important;
            margin: 12px 4px 6px 4px !important;
            /* Margin atas lebih besar untuk ruang Pin */
            overflow: visible !important;
        }

        .fc-event-main {
            padding: 0 !important;
            white-space: normal !important;
            overflow: visible !important;
        }

        .fc .fc-button {
            text-transform: capitalize !important;
        }

        /* --- MAGIC CSS: DESAIN STICKY NOTE --- */
        .sticky-wrapper {
            position: relative;
            padding-top: 6px;
        }

        /* Pin Merah di atas */
        .push-pin {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%) rotate(5deg);
            font-size: 20px;
            z-index: 20;
            filter: drop-shadow(2px 3px 2px rgba(0, 0, 0, 0.3));
            transition: transform 0.2s ease;
        }

        .sticky-note {
            position: relative;
            padding: 16px 10px 10px 10px;
            /* Sudut bawah dibikin melengkung asimetris agar terlihat asli */
            border-radius: 2px 2px 18px 4px;
            box-shadow: 2px 4px 6px rgba(0, 0, 0, 0.12);
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            z-index: 10;
            color: #ffffff;
            /* Asumsi background dari database berwarna gelap */
        }

        /* Pita gelap di bagian atas (bayangan tempat pin menancap) */
        .sticky-band {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 12px;
            background: rgba(0, 0, 0, 0.08);
            border-radius: 2px 2px 0 0;
        }

        /* Efek lengkungan/bayangan kertas di sudut kanan bawah */
        .sticky-note::after {
            content: "";
            position: absolute;
            z-index: -1;
            bottom: 3px;
            right: 4px;
            width: 50%;
            height: 20%;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3);
            transform: rotate(4deg);
            border-radius: 50%;
        }

        /* Animasi saat di-hover */
        .sticky-wrapper:hover .sticky-note {
            transform: scale(1.03) rotate(-1deg);
            box-shadow: 3px 8px 12px rgba(0, 0, 0, 0.2);
            z-index: 30;
        }

        .sticky-wrapper:hover .push-pin {
            transform: translateX(-50%) translateY(-2px) rotate(0deg);
        }

        /* 🚀 Tipografi Judul Konten (Kembali ke font bawaan Filament) */
        .sticky-title {
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            /* Standard Tailwind/Filament */
            font-size: 0.85rem;
            line-height: 1.3;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: 0.025em;
            /* Sedikit renggang agar mudah dibaca */
        }

        /* Desain badge tim agar rapi dan muat di note kecil */
        .sticky-team {
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            font-size: 0.65rem;
            border-top: 1px dashed rgba(255, 255, 255, 0.3);
            padding-top: 6px;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .team-badge {
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(0, 0, 0, 0.15);
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: 500;
            backdrop-filter: blur(2px);
        }

        /* MAGIC RESPONSIVE: Mengatur Header Kalender di Layar HP (Mobile) */
        @media (max-width: 768px) {
            .fc-toolbar {
                flex-direction: column !important;
                gap: 12px;
            }

            .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
                width: 100%;
                text-align: center;
            }

            .fc-toolbar-title {
                font-size: 1.25rem !important;
            }
        }
    </style>

    {{-- Pembungkus Kalender --}}
    <div
        class="bg-white dark:bg-gray-900 p-3 md:p-6 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 w-full">
        <div id='calendar' wire:ignore></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                contentHeight: 'auto',
                handleWindowResize: true,

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },

                buttonText: {
                    today: 'Hari Ini',
                },

                events: {!! $events !!},

                // 1. KLIK TANGGAL KOSONG
                dateClick: function(info) {
                    let createUrl =
                        "{{ route('filament.admin.resources.contents.create') }}?tanggal_kegiatan=" +
                        info.dateStr;
                    window.open(createUrl, "_self");
                },

                // 2. KLIK EVENT
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.open(info.event.url, "_self");
                    }
                },

                // 3. DESAIN KOTAK EVENT (STICKY NOTE)
                eventContent: function(arg) {
                    let planner = arg.event.extendedProps.planner || '-';
                    let editor = arg.event.extendedProps.editor || '-';
                    let admin = arg.event.extendedProps.admin || '-';

                    let html = `
                        <div class="sticky-wrapper" style="max-width: 100%;">
                            <div class="push-pin">📌</div>
                            <div class="sticky-note" style="background-color: ${arg.event.backgroundColor}; max-width: 100%;">
                                <div class="sticky-band"></div>

                                <div class="sticky-title">
                                    ${arg.event.title}
                                </div>

                                <div class="sticky-team">
                                    <div class="team-badge" title="Planner">
                                        <span>📝</span>
                                        <span class="truncate">${planner}</span>
                                    </div>
                                    <div class="team-badge" title="Editor">
                                        <span>🎬</span>
                                        <span class="truncate">${editor}</span>
                                    </div>
                                    <div class="team-badge" title="Admin">
                                        <span>🌐</span>
                                        <span class="truncate">${admin}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    return {
                        html: html
                    };
                }
            });

            calendar.render();

            // 🚀 JURUS AMPUH: Paksa kalender menghitung ulang ukuran layar
            // setelah 150 milidetik agar hari Sabtu tidak terpotong.
            setTimeout(function() {
                calendar.updateSize();
            }, 150);

            // Jaga-jaga jika tombol sidebar Filament ditekan (layar menyusut/melebar)
            window.addEventListener('resize', function() {
                calendar.updateSize();
            });
        });
    </script>
</x-filament-panels::page>
