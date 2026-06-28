<?php $__env->startSection('title', 'Kejuruan - ' . ($settings?->website_name ?? 'BPVP Pangkep')); ?>

<?php $__env->startSection('content'); ?>
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Informasi</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600">Kejuruan Pelatihan</span>
            </nav>

            <div class="space-y-6">
                
                <div class="border-b border-slate-200 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Program
                        Pelatihan</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kejuruan Pelatihan Aktif</h1>
                    <p class="text-xs text-slate-500 mt-1">Daftar kejuruan program pelatihan kerja terstandar kompetensi di
                        BPVP Pangkep.</p>
                </div>

                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $kejuruan ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $item = (object) $item;
                        ?>

                        
                        <div x-data="{ isOpen: false }" @keydown.escape.window="isOpen = false">

                            
                            <div @click="isOpen = true"
                                class="bg-white rounded-2xl border border-slate-200/60 shadow-xs overflow-hidden hover:shadow-md hover:border-blue-500/30 transition duration-300 group cursor-pointer h-full flex flex-col justify-between">
                                <div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->foto_kejuruan)): ?>
                                        <div
                                            class="w-full h-48 overflow-hidden bg-slate-100 border-b border-slate-100 relative">
                                            <img src="<?php echo e(asset('storage/' . $item->foto_kejuruan)); ?>"
                                                alt="<?php echo e($item->nama_kejuruan); ?>"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                            
                                            <div
                                                class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                                <span
                                                    class="bg-white/90 backdrop-blur-xs text-xs font-bold text-slate-800 px-3 py-1.5 rounded-lg shadow-sm">Lihat
                                                    Detail</span>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <div class="p-5">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($item->foto_kejuruan)): ?>
                                            <div
                                                class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <h3
                                            class="font-extrabold text-slate-900 text-base mb-2 group-hover:text-blue-600 transition-colors duration-300">
                                            <?php echo e($item->nama_kejuruan); ?>

                                        </h3>

                                        <div class="text-xs text-slate-500 mt-1 line-clamp-3 leading-relaxed font-normal">
                                            <?php echo e(strip_tags($item->deskripsi_kejuruan ?? 'Pelatihan berbasis kompetensi siap kerja.')); ?>

                                        </div>
                                    </div>
                                </div>

                                
                                <div
                                    class="px-5 pb-5 pt-2 flex items-center text-xs font-bold text-blue-600 gap-1.5 group-hover:gap-2.5 transition-all">
                                    <span>Selengkapnya</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </div>
                            </div>

                            
                            <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto"
                                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

                                
                                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="isOpen = false"></div>

                                
                                <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">

                                    
                                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl flex flex-col my-8"
                                        @click.away="isOpen = false" x-show="isOpen"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                                        
                                        <button @click="isOpen = false"
                                            class="absolute right-4 top-4 z-10 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md text-slate-500 hover:text-slate-800 flex items-center justify-center shadow-xs border border-slate-200/50 transition cursor-pointer">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->foto_kejuruan)): ?>
                                            <div
                                                class="w-full h-64 sm:h-80 overflow-hidden bg-slate-50 border-b border-slate-100">
                                                <img src="<?php echo e(asset('storage/' . $item->foto_kejuruan)); ?>"
                                                    alt="<?php echo e($item->nama_kejuruan); ?>" class="w-full h-full object-cover">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <div class="p-6 sm:p-8 space-y-4">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($item->foto_kejuruan)): ?>
                                                <div
                                                    class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                                                    <i class="fas fa-graduation-cap"></i>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <div>
                                                <span
                                                    class="text-[10px] font-bold text-blue-600 uppercase tracking-widest block mb-1">Detail
                                                    Program Pelatihan</span>
                                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                                    <?php echo e($item->nama_kejuruan); ?>

                                                </h2>
                                            </div>

                                            
                                            <div
                                                class="text-sm text-slate-600 leading-relaxed max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                                                <div class="prose prose-sm prose-slate max-w-none">
                                                    <?php echo $item->deskripsi_kejuruan ??
                                                        '<p class="italic text-slate-400">Belum ada rincian deskripsi resmi untuk kejuruan ini.</p>'; ?>

                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div
                                            class="bg-slate-50 px-6 py-4 flex justify-end rounded-b-2xl border-t border-slate-100">
                                            <button @click="isOpen = false"
                                                class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-5 text-xs rounded-xl transition cursor-pointer">
                                                Tutup Detail
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        
                        <div
                            class="col-span-full text-center py-16 bg-white rounded-3xl border border-dashed border-slate-200 p-8 shadow-xs">
                            <div
                                class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-4 border border-slate-100">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h4 class="font-bold text-slate-800 text-base">Belum Ada Program Kejuruan</h4>
                            <p class="text-xs text-slate-400 max-w-md mx-auto mt-1 leading-relaxed">
                                Daftar kejuruan program pelatihan reguler aktif saat ini belum diinput atau sedang dalam
                                proses pembaruan oleh admin balai.
                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/informasi/kejuruan.blade.php ENDPATH**/ ?>