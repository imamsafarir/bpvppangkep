<section class="py-10 bg-white overflow-hidden border-y border-slate-100">

    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 text-center">
        <h3 class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-[0.2em]">
            Telah Dipercaya & Bekerja Sama Dengan
        </h3>
    </div>

    <?php
        // Ambil data langsung dari Database
        $dataInformasi = \App\Models\Informasi::first();
        $kerjasama = $dataInformasi?->kerjasama ?? [];
        $jumlahMitra = count($kerjasama);
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jumlahMitra > 0): ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jumlahMitra <= 5): ?>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-center gap-10 sm:gap-16">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kerjasama; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div
                            class="flex items-center gap-3 grayscale hover:grayscale-0 opacity-70 hover:opacity-100 transition-all duration-300 cursor-pointer select-none">

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['logo'])): ?>
                                <img src="<?php echo e(asset('storage/' . $item['logo'])); ?>"
                                    alt="<?php echo e($item['nama_instansi'] ?? 'Mitra'); ?>"
                                    class="h-10 sm:h-12 w-auto max-w-[160px] object-contain flex-shrink-0" />
                            <?php else: ?>
                                <div
                                    class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-blue-600 shadow-inner border border-slate-200 flex-shrink-0">
                                    <i class="fas fa-building text-sm"></i>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['nama_instansi'])): ?>
                                <span
                                    class="text-base sm:text-lg font-black text-slate-800 whitespace-nowrap tracking-tight">
                                    <?php echo e($item['nama_instansi']); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            
            <?php
                // Trik Rahasia: Gandakan array 4x agar animasi tidak pernah terputus meskipun di layar super lebar
                $loopItems = array_merge($kerjasama, $kerjasama, $kerjasama, $kerjasama);
            ?>

            <div class="relative w-full flex overflow-x-hidden group">

                
                <div
                    class="absolute inset-y-0 left-0 w-40 sm:w-96 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none">
                </div>
                <div
                    class="absolute inset-y-0 right-0 w-40 sm:w-96 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none">
                </div>

                
                <style>
                    @keyframes scroll-x {
                        0% {
                            transform: translateX(0);
                        }

                        100% {
                            transform: translateX(-50%);
                        }

                        /* Translate -50% selalu pas karena array digandakan kelipatan genap (4x) */
                    }

                    .animate-scroll-x {
                        animation: scroll-x 40s linear infinite;
                        width: max-content;
                    }

                    /* Animasi berhenti jika disentuh mouse */
                    .group:hover .animate-scroll-x {
                        animation-play-state: paused;
                    }
                </style>

                
                <div class="animate-scroll-x flex items-center gap-12 sm:gap-16 pl-12 sm:pl-16">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $loopItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div
                            class="flex items-center gap-3 grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition-all duration-300 cursor-pointer select-none">

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['logo'])): ?>
                                <img src="<?php echo e(asset('storage/' . $item['logo'])); ?>"
                                    alt="<?php echo e($item['nama_instansi'] ?? 'Mitra'); ?>"
                                    class="h-10 sm:h-12 w-auto max-w-[140px] object-contain flex-shrink-0" />
                            <?php else: ?>
                                <div
                                    class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-blue-600 shadow-inner border border-slate-200 flex-shrink-0">
                                    <i class="fas fa-building text-sm"></i>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['nama_instansi'])): ?>
                                <span
                                    class="text-base sm:text-lg font-black text-slate-800 whitespace-nowrap tracking-tight">
                                    <?php echo e($item['nama_instansi']); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        
        <div class="text-center text-xs text-slate-400 italic py-6">
            Belum ada data instansi kerja sama yang ditambahkan.
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</section>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/components/home/partners.blade.php ENDPATH**/ ?>