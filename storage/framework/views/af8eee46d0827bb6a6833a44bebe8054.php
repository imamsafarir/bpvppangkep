<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['informasi']));

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

foreach (array_filter((['informasi']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<section id="documents" class="bg-slate-100/70 py-20 scroll-mt-20" x-data="{ activeInfoTab: 'berkala' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-6 pb-5 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">PPID
                    Utama</span>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Dokumen Keterbukaan Informasi</h3>
            </div>

            
            <div class="flex flex-wrap p-1 bg-slate-100 rounded-xl gap-1 self-start md:self-auto select-none">
                <button @click="activeInfoTab = 'berkala'"
                    :class="activeInfoTab === 'berkala' ? 'bg-blue-600 text-white font-bold shadow-xs' :
                        'text-slate-600 font-semibold hover:bg-slate-200/50'"
                    class="text-xs px-4 py-2.5 rounded-lg transition-all cursor-pointer">
                    🗓️ Berkala
                </button>
                <button @click="activeInfoTab = 'serta_merta'"
                    :class="activeInfoTab === 'serta_merta' ? 'bg-blue-600 text-white font-bold shadow-xs' :
                        'text-slate-600 font-semibold hover:bg-slate-200/50'"
                    class="text-xs px-4 py-2.5 rounded-lg transition-all cursor-pointer">
                    📢 Serta Merta
                </button>
                <button @click="activeInfoTab = 'setiap_saat'"
                    :class="activeInfoTab === 'setiap_saat' ? 'bg-blue-600 text-white font-bold shadow-xs' :
                        'text-slate-600 font-semibold hover:bg-slate-200/50'"
                    class="text-xs px-4 py-2.5 rounded-lg transition-all cursor-pointer">
                    ⏱️ Setiap Saat
                </button>
            </div>
        </div>

        
        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($informasi) && $informasi->count() > 0): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['berkala', 'serta_merta', 'setiap_saat']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $filteredDocs = $informasi->where('kategori', $tabKategori);
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $filteredDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div x-show="activeInfoTab === '<?php echo e($tabKategori); ?>'" x-transition
                            class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-xs hover:shadow-md hover:border-slate-300 transition flex flex-col justify-between group relative">
                            <div>
                                <div class="text-red-500 text-3xl mb-3"><i class="fas fa-file-pdf"></i></div>
                                <h4 class="font-extrabold text-slate-900 text-sm mt-2 line-clamp-1">
                                    <?php echo e($item->nama_dokumen); ?></h4>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-3 leading-relaxed font-normal">
                                    <?php echo e($item->deskripsi ?? 'Tidak ada deskripsi tambahan.'); ?>

                                </p>
                            </div>
                            <div class="flex justify-between items-center pt-4 mt-4 border-t border-slate-50">
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-tight"><?php echo e($item->created_at?->format('d/m/Y')); ?></span>
                                <a href="<?php echo e(route('download.informasi', $item->id)); ?>"
                                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-xs">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div x-show="activeInfoTab === '<?php echo e($tabKategori); ?>'"
                            class="col-span-full text-center py-12 text-xs font-medium text-slate-400 bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                            Belum ada berkas dokumen kategori <?php echo e(str_replace('_', ' ', $tabKategori)); ?> yang
                            diunggah.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php else: ?>
                <div
                    class="col-span-full text-center py-12 text-xs font-medium text-slate-400 bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                    Belum ada dokumen informasi publik yang tersedia.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/components/home/documents.blade.php ENDPATH**/ ?>