<?php $__env->startSection('title', 'Galeri - ' . ($settings?->website_name ?? 'BPVP Pangkep')); ?>

<?php $__env->startSection('content'); ?>
    <main class="pt-32 pb-16 min-h-screen bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            
            <nav
                class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-8 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-slate-500">Kabar Balai</span>
                <i class="fas fa-chevron-right text-[9px]"></i><span class="text-blue-600">Galeri Kegiatan</span>
            </nav>

            <div class="space-y-8">
                
                <div class="border-b border-slate-200 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Dokumentasi</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Galeri Foto Kegiatan</h1>
                    <p class="text-xs text-slate-500 mt-1">Dokumentasi visual serangkaian agenda, proses pelatihan, dan
                        momentum penting balai.</p>
                </div>

                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-y-10 gap-x-6 pt-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $galeri_list ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $galeri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $fotos = is_array($galeri->file_foto)
                                ? $galeri->file_foto
                                : json_decode($galeri->file_foto, true) ?? [];

                            $jumlahFoto = count($fotos);
                            $fotoSampul = $fotos[0] ?? null;
                        ?>

                        
                        <div x-data="{ isOpen: false, photoIndex: 0, totalPhotos: <?php echo e($jumlahFoto); ?> }" @keydown.escape.window="isOpen = false"
                            class="relative group select-none">

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jumlahFoto > 1): ?>
                                <div
                                    class="absolute inset-0 transform translate-x-2.5 -translate-y-2 bg-slate-300/60 border border-slate-400/20 rounded-2xl transition duration-300 group-hover:translate-x-4 group-hover:-translate-y-3.5 shadow-2xs">
                                </div>
                                <div
                                    class="absolute inset-0 transform translate-x-1.5 -translate-y-1 bg-slate-200 border border-slate-300/40 rounded-2xl transition duration-300 group-hover:translate-x-2 group-hover:-translate-y-2 shadow-xs">
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <div @click="isOpen = true; photoIndex = 0"
                                class="relative bg-white rounded-2xl border border-slate-200/70 p-2.5 shadow-xs transition duration-300 group-hover:border-blue-500/30 group-hover:shadow-md cursor-pointer flex flex-col justify-between h-full z-10">
                                <div>
                                    <div
                                        class="aspect-square bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 relative overflow-hidden group/img">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fotoSampul): ?>
                                            <img src="<?php echo e(asset('storage/' . $fotoSampul)); ?>"
                                                alt="<?php echo e($galeri->keterangan_galeri); ?>"
                                                class="w-full h-full object-cover group-hover:scale-102 transition duration-500">
                                        <?php else: ?>
                                            <div class="flex flex-col items-center gap-1">
                                                <i class="fas fa-image text-3xl text-slate-300"></i>
                                                <span class="text-[10px] text-slate-400">Kosong</span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <span
                                            class="absolute top-3 right-3 bg-slate-900/70 backdrop-blur-md text-white font-bold text-[10px] px-2 py-1 rounded-md tracking-wider flex items-center gap-1 shadow-xs">
                                            <i class="fas fa-images text-[9px]"></i> <?php echo e($jumlahFoto); ?> FOTO
                                        </span>

                                        <div
                                            class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                            <span
                                                class="bg-white/95 backdrop-blur-xs text-slate-800 text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-xs">Buka
                                                Album</span>
                                        </div>
                                    </div>

                                    <div class="p-3 space-y-1">
                                        <h4 class="font-extrabold text-slate-800 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors duration-300"
                                            title="<?php echo e($galeri->keterangan_galeri); ?>">
                                            <?php echo e($galeri->keterangan_galeri); ?>

                                        </h4>
                                    </div>
                                </div>

                                <div
                                    class="px-3 pb-2 pt-1 border-t border-slate-50 flex items-center text-[10px] font-bold text-slate-400 justify-between">
                                    <span class="uppercase tracking-tight"><i class="far fa-calendar-alt"></i>
                                        <?php echo e($galeri->created_at?->translatedFormat('d M Y') ?? ''); ?></span>
                                    <span
                                        class="text-blue-600 flex items-center gap-0.5 group-hover:gap-1 transition-all">Lihat
                                        <i class="fas fa-chevron-right text-[8px]"></i></span>
                                </div>
                            </div>

                            
                            <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto"
                                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

                                
                                <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-xs" @click="isOpen = false"></div>

                                
                                <div class="flex min-h-full items-center justify-center p-4 md:p-10 relative">

                                    
                                    <button x-show="totalPhotos > 1"
                                        @click.stop="photoIndex = (photoIndex === 0) ? totalPhotos - 1 : photoIndex - 1"
                                        class="fixed left-4 md:left-8 top-1/2 -translate-y-1/2 z-50 w-12 h-12 md:w-14 md:h-14 rounded-full bg-white/90 hover:bg-blue-600 text-slate-700 hover:text-white flex items-center justify-center shadow-2xl border border-slate-200/50 transition duration-300 cursor-pointer group">
                                        <i
                                            class="fas fa-chevron-left text-sm md:text-base group-hover:-translate-x-0.5 transition-transform"></i>
                                    </button>

                                    
                                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-6xl flex flex-col my-8 border border-slate-100"
                                        @click.away="isOpen = false" x-show="isOpen"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95">

                                        
                                        <div
                                            class="bg-slate-50/90 px-6 py-4 flex justify-between items-center border-b border-slate-200/60 sticky top-0 z-20 backdrop-blur-md">
                                            <div class="pr-8 overflow-hidden">
                                                <span
                                                    class="text-[10px] font-bold text-blue-600 uppercase tracking-widest block mb-0.5">Dokumentasi
                                                    Album Balai</span>
                                                <h2
                                                    class="text-sm md:text-base font-black text-slate-900 tracking-tight break-words line-clamp-1">
                                                    <?php echo e($galeri->keterangan_galeri); ?>

                                                </h2>
                                            </div>
                                            <button @click="isOpen = false"
                                                class="w-8 h-8 rounded-full bg-white text-slate-400 hover:text-slate-700 flex items-center justify-center transition cursor-pointer flex-shrink-0 border border-slate-200 shadow-2xs">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>

                                        
                                        <div
                                            class="w-full aspect-video bg-slate-950 flex items-center justify-center overflow-hidden relative group/nav min-h-[300px] sm:min-h-[450px] md:min-h-[550px]">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $fotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subIndex => $subFoto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <img src="<?php echo e(asset('storage/' . $subFoto)); ?>" alt="Dokumentasi Foto"
                                                    x-show="photoIndex === <?php echo e($subIndex); ?>"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 scale-98"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    class="w-full h-full object-contain max-h-[75vh]">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>

                                        
                                        <div
                                            class="bg-slate-50 px-6 py-4 flex justify-between items-center border-t border-slate-200/60 text-slate-800">
                                            <span
                                                class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">
                                                Tanggal Agenda: <?php echo e($galeri->created_at?->translatedFormat('d F Y') ?? ''); ?>

                                            </span>
                                            <span
                                                class="bg-blue-50 border border-blue-100 text-blue-600 px-3 py-1 rounded-md font-mono text-[10px] md:text-xs font-bold tracking-widest"
                                                x-text="(photoIndex + 1) + ' / ' + totalPhotos">
                                            </span>
                                        </div>

                                    </div>

                                    
                                    <button x-show="totalPhotos > 1"
                                        @click.stop="photoIndex = (photoIndex === totalPhotos - 1) ? 0 : photoIndex + 1"
                                        class="fixed right-4 md:right-8 top-1/2 -translate-y-1/2 z-50 w-12 h-12 md:w-14 md:h-14 rounded-full bg-white/90 hover:bg-blue-600 text-slate-700 hover:text-white flex items-center justify-center shadow-2xl border border-slate-200/50 transition duration-300 cursor-pointer group">
                                        <i
                                            class="fas fa-chevron-right text-sm md:text-base group-hover:translate-x-0.5 transition-transform"></i>
                                    </button>

                                </div>
                            </div>

                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        
                        <div
                            class="col-span-full bg-white rounded-3xl border border-slate-200/60 p-16 text-center shadow-2xs border-dashed">
                            <div
                                class="w-14 h-14 bg-slate-50 text-slate-400 flex items-center justify-center text-2xl rounded-2xl mx-auto mb-4 border border-slate-100">
                                <i class="fas fa-images"></i>
                            </div>
                            <h4 class="font-bold text-slate-800 text-sm">Belum Ada Galeri Foto</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 leading-relaxed">
                                Dokumentasi rangkuman potret kegiatan penunjang kompetensi kerja belum diunggah oleh pihak
                                admin balai.
                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/berita/galeri.blade.php ENDPATH**/ ?>