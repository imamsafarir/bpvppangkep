<?php $__env->startSection('title', $berita->judul_berita . ' - ' . ($settings?->website_name ?? 'BPVP Pangkep')); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* === PROTEKSI LAYOUT KONTEN BERITA === */
        .prose {
            max-width: 100%;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
        }

        .prose * {
            max-width: 100%;
            box-sizing: border-box;
            word-break: break-word;
        }

        /* === Gambar === */
        .prose img {
            max-width: 100%;
            height: auto;
            border-radius: 1rem;
            margin-left: auto;
            margin-right: auto;
        }

        /* === Paragraf === */
        .prose p {
            margin-bottom: 1.25rem;
            line-height: 1.75;
        }

        /* === List === */
        .prose ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .prose ol {
            list-style-type: decimal;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        /* === Tabel === */
        .prose table {
            display: block;
            width: 100%;
            overflow-x: auto;
            border-collapse: collapse;
        }

        /* === Code / Pre === */
        .prose pre,
        .prose code {
            white-space: pre-wrap;
            word-break: break-word;
            overflow-x: auto;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <main class="pt-32 pb-16 min-h-screen bg-slate-50/50">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 uppercase tracking-wider select-none">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <a href="<?php echo e(route('berita.index')); ?>" class="hover:text-blue-600 transition-colors">Berita</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600 truncate max-w-[200px] sm:max-w-xs"><?php echo e($berita->judul_berita); ?></span>
            </nav>

            
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                
                <div class="lg:col-span-8 space-y-6">
                    <article class="bg-white rounded-3xl border border-slate-200/60 shadow-xs overflow-hidden">

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($berita->file_foto): ?>
                            <?php
                                $foto = $berita->file_foto;
                                if (is_string($foto)) {
                                    $foto = json_decode($foto, true) ?? [$foto];
                                }
                                $gambarUtama = is_array($foto) ? $foto[0] ?? null : $foto;
                            ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gambarUtama): ?>
                                <div class="w-full aspect-video bg-slate-100 overflow-hidden">
                                    <img src="<?php echo e(asset('storage/' . $gambarUtama)); ?>" alt="<?php echo e($berita->judul_berita); ?>"
                                        class="w-full h-full object-cover">
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="p-6 sm:p-10 space-y-6">
                            
                            <div class="border-b border-slate-100 pb-5 space-y-2">
                                <span
                                    class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full uppercase tracking-wider inline-block">
                                    Artikel Berita
                                </span>
                                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                                    <?php echo e($berita->judul_berita); ?>

                                </h1>
                                <div class="flex items-center gap-2 text-xs text-slate-400 pt-1 font-medium">
                                    <i class="far fa-calendar-alt"></i>
                                    <span><?php echo e($berita->created_at?->translatedFormat('d F Y H:i') ?? 'Baru saja'); ?>

                                        WITA</span>
                                    <span class="text-slate-200">•</span>
                                    <i class="far fa-user"></i>
                                    <span>Administrator</span>
                                </div>
                            </div>

                            
                            <div class="text-slate-700 text-sm sm:text-base leading-relaxed max-w-none prose prose-slate">
                                <?php echo $berita->konten_berita; ?>

                            </div>

                            
                            <div class="pt-6 border-t border-slate-100 space-y-3" x-data="{ copied: false }">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Bagikan Berita Ini
                                </h4>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="https://api.whatsapp.com/send?text=<?php echo e(rawurlencode($berita->judul_berita . ' - ' . url()->current())); ?>"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-xl font-bold text-xs transition duration-300">
                                        <i class="fab fa-whatsapp text-sm"></i> WhatsApp
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(rawurlencode(url()->current())); ?>"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-3 py-2 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-xl font-bold text-xs transition duration-300">
                                        <i class="fab fa-facebook-f text-sm"></i> Facebook
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo e(rawurlencode(url()->current())); ?>&text=<?php echo e(rawurlencode($berita->judul_berita)); ?>"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 hover:bg-slate-900 text-slate-800 hover:text-white rounded-xl font-bold text-xs transition duration-300">
                                        <i class="fab fa-x-twitter text-sm"></i> Twitter
                                    </a>
                                    <button
                                        @click="
                                        if (navigator.clipboard && window.isSecureContext) {
                                        // Jalur utama untuk HTTPS
                                        navigator.clipboard.writeText(window.location.href);
                                        } else {
                                        // Jalur cadangan (Fallback) jika diakses via HTTP biasa
                                        let textArea = document.createElement('textarea');
                                        textArea.value = window.location.href;
                                        textArea.style.position = 'fixed';
                                        textArea.style.left = '-999999px';
                                        document.body.appendChild(textArea);
                                        textArea.focus();
                                        textArea.select();
                                        try {
                                        document.execCommand('copy');
                                        } catch (err) {console.error('Gagal menyalin tautan: ', err);}
                                        document.body.removeChild(textArea);}
                                        copied = true;
                                        setTimeout(() => copied = false, 2500)"
                                        class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs transition duration-300 cursor-pointer"
                                        :class="copied ? 'bg-green-500 text-black' : 'hover:bg-slate-200'">
                                        <i class="fas text-sm" :class="copied ? 'fa-check' : 'fa-link'"></i>
                                        <span x-text="copied ? 'Tautan Tersalin!' : 'Salin Link'"></span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </article>

                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        
                        <div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($prevBerita) && $prevBerita): ?>
                                <a href="<?php echo e(route('berita.show', $prevBerita->id)); ?>"
                                    class="group block p-4 bg-white hover:bg-blue-50/50 border border-slate-200/60 rounded-2xl transition duration-300 shadow-2xs">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                        <i class="fas fa-arrow-left"></i> Berita Sebelumnya
                                    </span>
                                    <span
                                        class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition-colors line-clamp-1">
                                        <?php echo e($prevBerita->judul_berita); ?>

                                    </span>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div class="text-right">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($nextBerita) && $nextBerita): ?>
                                <a href="<?php echo e(route('berita.show', $nextBerita->id)); ?>"
                                    class="group block p-4 bg-white hover:bg-blue-50/50 border border-slate-200/60 rounded-2xl transition duration-300 shadow-2xs">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                        Berita Selanjutnya <i class="fas fa-arrow-right"></i>
                                    </span>
                                    <span
                                        class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition-colors line-clamp-1">
                                        <?php echo e($nextBerita->judul_berita); ?>

                                    </span>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                
                
                <div class="lg:col-span-4 lg:sticky lg:top-32 space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($beritaTerkait) && $beritaTerkait->count() > 0): ?>
                        <h3 class="text-base font-black text-slate-900 tracking-tight flex items-center gap-2 px-1">
                            <i class="fas fa-newspaper text-blue-600"></i> Baca Berita Lainnya
                        </h3>

                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $beritaTerkait; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $terkait): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $subFoto = $terkait->file_foto;
                                    if (is_string($subFoto)) {
                                        $subFoto = json_decode($subFoto, true) ?? [$subFoto];
                                    }
                                    $gambarTerkait = is_array($subFoto) ? $subFoto[0] ?? null : $subFoto;
                                ?>

                                <a href="<?php echo e(route('berita.show', $terkait->id)); ?>"
                                    class="bg-white rounded-2xl border border-slate-200/60 shadow-2xs overflow-hidden group hover:border-blue-500/30 hover:shadow-md transition duration-300 flex flex-col justify-between">
                                    <div>
                                        <div class="w-full aspect-video bg-slate-100 overflow-hidden relative">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gambarTerkait): ?>
                                                <img src="<?php echo e(asset('storage/' . $gambarTerkait)); ?>"
                                                    alt="<?php echo e($terkait->judul_berita); ?>"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                            <?php else: ?>
                                                <div
                                                    class="w-full h-full flex flex-col items-center justify-center text-slate-300 bg-slate-50 gap-1 text-xs">
                                                    <i class="fas fa-image text-xl"></i>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div class="p-4">
                                            <h4
                                                class="font-bold text-slate-800 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors">
                                                <?php echo e($terkait->judul_berita); ?>

                                            </h4>
                                        </div>
                                    </div>
                                    <div
                                        class="px-4 pb-4 pt-1 flex items-center justify-between text-[10px] font-medium text-slate-400">
                                        <span><?php echo e($terkait->created_at?->translatedFormat('d M Y')); ?></span>
                                        <span
                                            class="text-blue-600 font-bold group-hover:translate-x-0.5 transition-transform">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </div>
                                </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/berita/show.blade.php ENDPATH**/ ?>