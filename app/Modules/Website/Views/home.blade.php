@extends('website::layouts.app')

@section('title', ($settings?->website_name ?? 'BPVP Pangkep') . ' - Beranda')

@push('styles')
    <style>
        /* Efek Kursor Mengetik Berkedip & Animasi Lainnya Tetap Disini */
        @keyframes blink-caret {

            from,
            to {
                border-color: transparent
            }

            50% {
                border-color: #fbbf24;
            }
        }

        @keyframes fade-in-up {
            0% {
                opacity: 0;
                transform: translate3d(0, 30px, 0);
            }

            100% {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes fade-in-down {
            0% {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }

            100% {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes text-shimmer {
            0% {
                background-position: 0% center;
            }

            100% {
                background-position: -200% center;
            }
        }

        .animate-caret {
            animation: blink-caret 0.75s step-end infinite;
        }

        .animate-fade-in-up {
            animation: fade-in-up 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-fade-in-down {
            animation: fade-in-down 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-text-shimmer {
            animation: text-shimmer 4s linear infinite;
        }
    </style>
@endpush

@section('content')
    <div class="relative w-full overflow-hidden">

        {{-- Panggil semua komponen dengan parameter yang dibutuhkan --}}
        <x-home.hero :settings="$settings" />

        <x-home.cta-pelatihan />

        <x-home.berita :berita="$berita_terbaru" />

        <x-home.about :settings="$settings" />

        <x-home.statistik :total-informasi="$total_informasi" :total-jdih="$total_jdih" :total-berita="$total_berita" :total-unduhan="$total_unduhan" :total-kunjungan="$total_kunjungan" />

        <x-home.documents :informasi="$informasi" />

        {{-- <x-home.social-hub :settings="$settings" /> --}}

        <x-home.faq />

        <x-home.partners />

        <x-home.popup :settings="$settings" />

    </div>
@endsection

