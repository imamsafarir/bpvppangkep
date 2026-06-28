<?php $__env->startSection('title', 'Testimoni - ' . ($settings?->website_name ?? 'BPVP Pangkep')); ?>

<?php $__env->startSection('content'); ?>
    <main class="pt-32 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Informasi</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600">Testimoni</span>
            </nav>

            <div class="space-y-8">
                
                <div class="border-b border-slate-200 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Ulasan Peserta</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Apa Kata Mereka Tentang BPVP
                        Pangkep?</h1>
                    <p class="text-xs text-slate-500 mt-1">Ulasan jujur dan kisah sukses langsung dari alumni setelah
                        mengikuti program pelatihan vokasi.</p>
                </div>

                <?php
                    $jumlahTestimoni = count($testimoni ?? []);
                ?>

                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-data="{ activeIndex: null, totalItems: <?php echo e($jumlahTestimoni); ?> }"
                    @keydown.escape.window="activeIndex = null">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $testimoni ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $item = (object) $item;
                        ?>

                        
                        <div @click="activeIndex = <?php echo e($index); ?>"
                            class="bg-white rounded-2xl border border-slate-200/60 shadow-xs p-6 flex flex-col justify-between relative group hover:shadow-md hover:border-blue-500/30 transition duration-300 cursor-pointer select-none h-full">

                            
                            <div
                                class="absolute top-6 right-6 text-slate-100 text-5xl font-serif pointer-events-none select-none group-hover:text-blue-50/70 transition-colors duration-300">
                                “
                            </div>

                            <div class="space-y-4">
                                
                                <div
                                    class="text-xs text-slate-600 leading-relaxed italic font-normal line-clamp-4 break-words">
                                    "<?php echo e(strip_tags($item->isi_testimoni ?? 'Tidak ada ulasan tertulis.')); ?>"
                                </div>
                            </div>

                            
                            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-slate-100 overflow-hidden">
                                <div
                                    class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->foto_alumni)): ?>
                                        <img src="<?php echo e(asset('storage/' . $item->foto_alumni)); ?>"
                                            alt="<?php echo e($item->nama_alumni); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div
                                            class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-500 text-sm font-bold">
                                            <?php echo e(strtoupper(substr($item->nama_alumni ?? 'A', 0, 1))); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="overflow-hidden">
                                    <h5
                                        class="font-extrabold text-slate-900 text-xs truncate group-hover:text-blue-600 transition-colors">
                                        <?php echo e($item->nama_alumni); ?></h5>
                                    <p class="text-[10px] text-slate-400 truncate mt-0.5"><?php echo e($item->pekerjaan); ?></p>
                                </div>
                            </div>

                        </div>

                        
                        <div x-show="activeIndex === <?php echo e($index); ?>" class="fixed inset-0 z-50 overflow-y-auto"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

                            
                            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="activeIndex = null"></div>

                            
                            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 relative">

                                
                                <button @click.stop="activeIndex = (activeIndex === 0) ? totalItems - 1 : activeIndex - 1"
                                    class="fixed left-4 md:left-8 top-1/2 -translate-y-1/2 z-50 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/90 text-slate-700 hover:bg-blue-600 hover:text-white flex items-center justify-center shadow-lg border border-slate-200/50 transition cursor-pointer">
                                    <i class="fas fa-chevron-left text-sm md:text-base"></i>
                                </button>

                                
                                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-xl flex flex-col my-8 z-20 break-words"
                                    @click.away="activeIndex = null">

                                    
                                    <button @click="activeIndex = null"
                                        class="absolute right-4 top-4 z-30 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md text-slate-500 hover:text-slate-800 flex items-center justify-center shadow-xs border border-slate-200/50 transition cursor-pointer">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>

                                    
                                    <div class="p-6 sm:p-8 space-y-6 pt-10">

                                        
                                        <div
                                            class="text-blue-500/20 text-6xl font-serif h-4 -mb-4 select-none pointer-events-none">
                                            “</div>

                                        
                                        <div
                                            class="text-sm sm:text-base text-slate-700 leading-relaxed italic max-h-[35vh] overflow-y-auto pr-2 custom-scrollbar break-words">
                                            <div
                                                class="prose prose-sm prose-slate max-w-none break-words whitespace-normal">
                                                <?php echo $item->isi_testimoni ?? '<p class="italic text-slate-400">Belum ada teks ulasan resmi.</p>'; ?>

                                            </div>
                                        </div>

                                        
                                        <div class="flex items-center gap-4 pt-5 border-t border-slate-100">
                                            <div
                                                class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->foto_alumni)): ?>
                                                    <img src="<?php echo e(asset('storage/' . $item->foto_alumni)); ?>"
                                                        alt="<?php echo e($item->nama_alumni); ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div
                                                        class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-500 text-base font-bold">
                                                        <?php echo e(strtoupper(substr($item->nama_alumni ?? 'A', 0, 1))); ?>

                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <div class="overflow-hidden">
                                                <h4 class="font-black text-slate-900 text-sm tracking-tight">
                                                    <?php echo e($item->nama_alumni); ?></h4>
                                                <p class="text-xs text-slate-400 mt-0.5"><?php echo e($item->pekerjaan); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div
                                        class="bg-slate-50 px-6 py-4 flex justify-between items-center rounded-b-2xl border-t border-slate-100 select-none">
                                        <span class="text-[10px] sm:text-xs font-bold text-slate-400">
                                            Ulasan <?php echo e($index + 1); ?> dari <?php echo e($jumlahTestimoni); ?> Testimoni
                                        </span>
                                        <button @click="activeIndex = null"
                                            class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-5 text-xs rounded-xl transition cursor-pointer">
                                            Tutup
                                        </button>
                                    </div>

                                </div>

                                
                                <button @click.stop="activeIndex = (activeIndex === totalItems - 1) ? 0 : activeIndex + 1"
                                    class="fixed right-4 md:right-8 top-1/2 -translate-y-1/2 z-50 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/90 text-slate-700 hover:bg-blue-600 hover:text-white flex items-center justify-center shadow-lg border border-slate-200/50 transition cursor-pointer">
                                    <i class="fas fa-chevron-right text-sm md:text-base"></i>
                                </button>

                            </div>
                        </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        
                        <div
                            class="col-span-1 sm:col-span-2 lg:col-span-3 bg-white rounded-3xl border border-slate-200/60 p-12 text-center shadow-xs">
                            <div
                                class="w-16 h-16 bg-slate-50 text-slate-400 flex items-center justify-center text-2xl rounded-2xl mx-auto mb-4 border border-slate-100">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Belum Ada Ulasan</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">
                                Lembar ulasan testimoni alumni pelatihan belum diisi melalui panel admin balai.
                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/informasi/testimoni.blade.php ENDPATH**/ ?>