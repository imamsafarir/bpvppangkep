<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['berita']));

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

foreach (array_filter((['berita']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<section id="berita-home" class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-10 pb-5 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Kabar
                    Balai</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Berita & Artikel Terkini
                </h2>
            </div>
            <a href="<?php echo e(route('berita.index')); ?>"
                class="text-xs font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wider flex items-center gap-1 transition-colors">
                Lihat Semua Berita <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($berita) && $berita->count() > 0): ?>
                <?php $beritaUtama = $berita->first(); ?>

                
                <div class="lg:col-span-7 group relative flex flex-col space-y-4 break-words min-w-0">
                    <div
                        class="w-full aspect-video bg-slate-100 rounded-2xl overflow-hidden border border-slate-200/50 relative shadow-xs">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($beritaUtama->file_foto): ?>
                            <img src="<?php echo e(asset('storage/' . (is_array($beritaUtama->file_foto) ? $beritaUtama->file_foto[0] : json_decode($beritaUtama->file_foto)[0] ?? $beritaUtama->file_foto))); ?>"
                                alt="Berita Utama"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500 ease-out">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="absolute top-4 left-4 z-20">
                            <span
                                class="text-[10px] font-black uppercase tracking-wider bg-blue-600 text-white px-3 py-1 rounded-md shadow-xs select-none">Sorotan</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 text-xs text-slate-400 font-medium select-none">
                            <span><?php echo e($beritaUtama->created_at?->translatedFormat('d M Y')); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($beritaUtama->tags)): ?>
                                <span
                                    class="text-blue-600 font-bold">#<?php echo e(is_array($beritaUtama->tags) ? $beritaUtama->tags[0] : $beritaUtama->tags); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <a href="<?php echo e(route('berita.show', $beritaUtama->id)); ?>" class="block">
                            <h3
                                class="text-xl sm:text-2xl font-black text-slate-900 leading-tight hover:text-blue-600 transition-colors line-clamp-2 break-words">
                                <?php echo e($beritaUtama->judul_berita); ?>

                            </h3>
                        </a>
                        
                        <p
                            class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal line-clamp-3 break-words whitespace-normal">
                            <?php echo e(strip_tags($beritaUtama->konten_berita)); ?>

                        </p>
                    </div>
                </div>

                
                <div class="lg:col-span-5 space-y-6 divide-y divide-slate-100 lg:divide-y-0 min-w-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $berita->skip(1)->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subBerita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex gap-4 items-center pt-4 first:pt-0 lg:pt-0 group break-words min-w-0">
                            <div
                                class="w-24 sm:w-28 aspect-video bg-slate-100 rounded-xl overflow-hidden flex-shrink-0 border border-slate-200/40 shadow-2xs">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subBerita->file_foto): ?>
                                    <img src="<?php echo e(asset('storage/' . (is_array($subBerita->file_foto) ? $subBerita->file_foto[0] : json_decode($subBerita->file_foto)[0] ?? $subBerita->file_foto))); ?>"
                                        alt="Thumb"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300 ease-out">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="space-y-1 min-w-0 flex-1">
                                <span
                                    class="text-[10px] font-bold text-slate-400 block select-none"><?php echo e($subBerita->created_at?->translatedFormat('d M Y')); ?></span>
                                <a href="<?php echo e(route('berita.show', $subBerita->id)); ?>" class="block">
                                    <h4
                                        class="font-extrabold text-xs sm:text-sm text-slate-900 leading-snug hover:text-blue-600 transition-colors line-clamp-2 break-words">
                                        <?php echo e($subBerita->judul_berita); ?>

                                    </h4>
                                </a>
                                
                                <p
                                    class="text-[11px] text-slate-400 font-normal line-clamp-1 break-words whitespace-normal">
                                    <?php echo e(strip_tags($subBerita->konten_berita)); ?>

                                </p>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                <div
                    class="col-span-full text-center py-12 border border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                    <p class="text-slate-400 italic text-sm">Belum ada unggahan artikel berita di backend.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/components/home/berita.blade.php ENDPATH**/ ?>