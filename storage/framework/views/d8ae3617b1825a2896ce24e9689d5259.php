<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['totalInformasi', 'totalJdih', 'totalBerita', 'totalUnduhan', 'totalKunjungan']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['totalInformasi', 'totalJdih', 'totalBerita', 'totalUnduhan', 'totalKunjungan']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section id="statistik" class="bg-slate-100/70 py-16 scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center lg:text-left">
            <h3
                class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center justify-center lg:justify-start gap-2">
                <i class="fas fa-chart-pie text-blue-600"></i> Statistik Layanan Publik
            </h3>
            <p class="text-xs text-slate-500 mt-1">Transparansi jumlah ketersediaan data berkas penunjang PPID dan
                akuntabilitas informasi balai.</p>
        </div>

        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">

            
            <a href="#documents"
                class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/50 shadow-xs text-center flex flex-col items-center group hover:border-blue-500/30 hover:shadow-md transition duration-300 cursor-pointer">
                <div
                    class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-lg mb-4 group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight"><?php echo e($totalInformasi ?? 0); ?>

                </h2>
                <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Dokumen PPID
                </p>
            </a>

            
            <a href="<?php echo e(url('/jdih')); ?>"
                class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/50 shadow-xs text-center flex flex-col items-center group hover:border-emerald-500/30 hover:shadow-md transition duration-300 cursor-pointer">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-lg mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight"><?php echo e($totalJdih ?? 0); ?></h2>
                <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Produk Hukum
                </p>
            </a>

            
            <a href="<?php echo e(url('/berita-informasi/daftar-berita')); ?>"
                class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/50 shadow-xs text-center flex flex-col items-center group hover:border-indigo-500/30 hover:shadow-md transition duration-300 cursor-pointer">
                <div
                    class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-lg mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight"><?php echo e($totalBerita ?? 0); ?></h2>
                <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Berita &
                    Artikel</p>
            </a>

            
            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/50 shadow-xs text-center flex flex-col items-center hover:border-amber-500/30 transition duration-300">
                <div
                    class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-lg mb-4">
                    <i class="fas fa-cloud-download-alt"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight"><?php echo e($totalUnduhan ?? 0); ?></h2>
                <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Total Unduhan
                </p>
            </div>

            
            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/50 shadow-xs text-center flex flex-col items-center hover:border-rose-500/30 transition duration-300">
                <div
                    class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-lg mb-4">
                    <i class="fas fa-eye"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight"><?php echo e($totalKunjungan ?? 0); ?>

                </h2>
                <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Pengunjung</p>
            </div>

        </div>
    </div>
</section>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/components/home/statistik.blade.php ENDPATH**/ ?>