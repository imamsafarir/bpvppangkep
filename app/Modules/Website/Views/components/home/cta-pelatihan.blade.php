{{-- Section utama tetap melebar 100% dengan warna background solid --}}
<section class="w-full bg-[#1e40ae]">

    {{-- Container Gambar Latar --}}
    <div class="w-full mx-auto bg-cover bg-[right_-120px_center] sm:bg-center bg-no-repeat flex items-center h-auto py-16 sm:py-0 sm:h-[510.396px]"
        style="max-width: 1666.670px; background-image: url('https://skillhub.kemnaker.go.id/assets/skillhub/skillhub-cta.png');">

        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Grid Pembagi Layout --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">

                {{-- Elemen Pembungkus Konten Utama --}}
                <div class="text-white max-w-[85%] sm:max-w-none">

                    {{-- 🟢 PERBAIKAN STRUKTUR: Mengubah div menjadi tag semantik h2 dan p tanpa mengubah class bawaan --}}
                    <div>
                        <h2 class="text-xl sm:text-3xl font-bold leading-tight">
                            Transformasikan hidup kamu melalui pelatihan
                        </h2>
                        <p class="text-sm sm:text-base pt-6 opacity-70 leading-relaxed">
                            Temukan pelatihan yang kamu minati dan sukai sekarang juga.
                        </p>
                    </div>

                    {{-- Spacing Tombol (pt-10) & Ukuran Tombol Proporsional --}}
                    <div class="pt-10">
                        <a href="https://skillhub.kemnaker.go.id/" target="_blank"
                            class="inline-flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm sm:text-base h-10 sm:h-12 px-6 rounded-xl transition-all duration-300 shadow-md gap-2 group">
                            <span>Daftar dan Ikuti Pelatihan</span>
                            <i
                                class="fas fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
