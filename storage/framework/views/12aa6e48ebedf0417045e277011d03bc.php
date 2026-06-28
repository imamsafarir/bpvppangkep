<!DOCTYPE html>


<?php $__env->startSection('title', 'Maklumat Pelayanan - ' . ($settings?->website_name ?? 'BPVP Pangkep')); ?>

<?php $__env->startSection('content'); ?>
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-slate-500">Pelayanan</span>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-blue-600">Maklumat Pelayanan</span>
            </nav>

            <div class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xs p-6 sm:p-10 space-y-6">
                    <div class="border-b border-slate-100 pb-5">
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Janji
                            Layanan</span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Maklumat Pelayanan
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">Pernyataan tertulis mengenai kesanggupan dan kewajiban
                            memberikan pelayanan dengan standar yang ditetapkan.</p>
                    </div>

                    <?php
                        $maklumatRaw = $pelayanan?->maklumat_pelayanan;

                        if (is_string($maklumatRaw)) {
                            $decoded = json_decode($maklumatRaw, true);
                            $maklumatList = is_array($decoded) ? $decoded : [];
                        } else {
                            $maklumatList = is_array($maklumatRaw) ? $maklumatRaw : [];
                        }
                    ?>

                    <div class="space-y-8">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $maklumatList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $item = (object) $item;
                                $extension = !empty($item->file_maklumat)
                                    ? pathinfo($item->file_maklumat, PATHINFO_EXTENSION)
                                    : '';
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                            ?>

                            <div
                                class="p-6 bg-slate-50 rounded-2xl border border-slate-200/50 space-y-6 hover:border-blue-500/20 transition-all">
                                
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/60 pb-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">
                                            <i class="fas fa-scroll"></i>
                                        </div>
                                        <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                            <?php echo e($item->judul_maklumat ?? 'Maklumat Pelayanan'); ?></h3>
                                    </div>

                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->file_maklumat) && !$isImage): ?>
                                        <a href="<?php echo e(asset('storage/' . $item->file_maklumat)); ?>" target="_blank"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs shrink-0">
                                            <i class="fas fa-file-pdf"></i> Lihat / Unduh PDF
                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->file_maklumat) && $isImage): ?>
                                    <div
                                        class="w-full overflow-hidden rounded-xl border border-slate-200/80 bg-white p-2 shadow-xs">
                                        <img src="<?php echo e(asset('storage/' . $item->file_maklumat)); ?>"
                                            alt="<?php echo e($item->judul_maklumat); ?>"
                                            class="w-full h-auto object-contain max-h-[800px] rounded-lg mx-auto">
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->keterangan_maklumat)): ?>
                                    <div
                                        class="bg-white p-5 rounded-xl border border-slate-200/40 text-xs sm:text-sm text-slate-600 leading-relaxed text-justify prose prose-slate max-w-none shadow-2xs">
                                        <?php echo $item->keterangan_maklumat; ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="text-center py-12">
                                <div
                                    class="w-16 h-16 bg-slate-50 text-slate-400 flex items-center justify-center text-2xl rounded-2xl mx-auto mb-4">
                                    <i class="fas fa-comment-slash"></i>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800">Data Belum Tersedia</h3>
                                <p class="text-xs text-slate-400 mt-1">Dokumen maklumat pelayanan resmi belum diisi di
                                    panel admin.</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/pelayanan-publik/maklumat.blade.php ENDPATH**/ ?>