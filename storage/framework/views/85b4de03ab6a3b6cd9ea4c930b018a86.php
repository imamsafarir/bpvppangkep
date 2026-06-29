<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['settings']));

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

foreach (array_filter((['settings']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<section class="w-full relative overflow-hidden bg-slate-950 h-[60vh] sm:h-[100dvh]" x-data="{
    currentSlider: 0,
    totalSliders: <?php echo e($settings?->sliders ? count($settings->sliders) : 1); ?>,
    autoplayInterval: null,
    startAutoplay() {
        if (this.totalSliders > 1 && !this.autoplayInterval) {
            this.autoplayInterval = setInterval(() => {
                this.currentSlider = (this.currentSlider + 1) % this.totalSliders
            }, 5000);
        }
    },
    stopAutoplay() {
        clearInterval(this.autoplayInterval);
        this.autoplayInterval = null;
    }
}"
    x-init="startAutoplay()" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">

    
    <div class="w-full h-full relative">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings?->sliders && count($settings->sliders) > 0): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $settings->sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div x-show="currentSlider === <?php echo e($index); ?>" x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 scale-102 blur-xs"
                    x-transition:enter-end="opacity-100 scale-100 blur-none"
                    x-transition:leave="transition ease-in duration-800" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="absolute inset-0 w-full h-full">
                    <img src="<?php echo e(asset('storage/' . $slide)); ?>" alt="Slider Banner"
                        class="w-full h-full object-cover object-center transform transition-transform duration-500">
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php else: ?>
            <div
                class="absolute inset-0 bg-gradient-to-br from-slate-900 to-blue-950 flex items-center justify-center text-white/40 text-sm">
                <i class="fas fa-images mr-2 text-base animate-pulse"></i> Belum ada gambar cover slider.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div
        class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-slate-950/40 mix-blend-multiply z-10">
    </div>

    
    <div class="absolute inset-0 z-20 flex items-center justify-center px-4 overflow-hidden" x-data="{
        text: '',
        words: ['Kompeten', 'Unggul', 'Siap Kerja', 'Berproduktivitas Tinggi'],
        wordIndex: 0,
        charIndex: 0,
        isDeleting: false,
        typeEffect() {
            let currentWord = this.words[this.wordIndex];
            if (this.isDeleting) {
                this.text = currentWord.substring(0, this.charIndex - 1);
                this.charIndex--;
            } else {
                this.text = currentWord.substring(0, this.charIndex + 1);
                this.charIndex++;
            }
            let typeSpeed = this.isDeleting ? 40 : 80;
            if (!this.isDeleting && this.charIndex === currentWord.length) {
                typeSpeed = 2200;
                this.isDeleting = true;
            } else if (this.isDeleting && this.text === '') {
                this.isDeleting = false;
                this.wordIndex = (this.wordIndex + 1) % this.words.length;
                typeSpeed = 400;
            }
            setTimeout(() => this.typeEffect(), typeSpeed);
        }
    }"
        x-init="setTimeout(() => typeEffect(), 1200)">

        <div class="absolute inset-0 pointer-events-none select-none z-0">
            <div class="absolute w-80 h-80 bg-blue-500/15 rounded-full blur-3xl -top-16 -left-16 animate-pulse"
                style="animation-duration: 7s;"></div>
            <div class="absolute w-[450px] h-[450px] bg-amber-500/5 rounded-full blur-3xl bottom-5 right-5 animate-pulse"
                style="animation-duration: 11s;"></div>
        </div>

        <div class="text-center max-w-7xl space-y-3 sm:space-y-5 z-10 px-4 w-full mx-auto overflow-hidden">
            <h1
                class="text-xs sm:text-2xl md:text-3xl lg:text-4xl font-extrabold tracking-tight text-white drop-shadow-[0_4px_12px_rgba(0,0,0,0.7)] uppercase">
                Selamat Datang di Situs Resmi</h1>
            <div
                class="font-black bg-gradient-to-r from-amber-300 via-yellow-100 to-amber-400 bg-clip-text text-transparent drop-shadow-[0_4px_10px_rgba(0,0,0,0.6)] leading-tight tracking-tight uppercase flex flex-col items-center gap-1 sm:gap-2 w-full">
                <span
                    class="block text-sm sm:text-xl md:text-2xl lg:text-4xl xl:text-5xl max-w-full tracking-tighter sm:tracking-tight break-words text-center">Balai
                    Pelatihan Vokasi dan Produktivitas</span>
                <span
                    class="block text-base sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl text-amber-400 font-black tracking-normal">Pangkajene
                    dan Kepulauan</span>
            </div>
            <div class="text-[11px] sm:text-base md:text-lg font-medium text-slate-200/90 tracking-wide">
                Mewujudkan Tenaga Kerja yang <span
                    class="text-amber-400 font-black border-r-3 border-amber-400 pl-1 pb-0.5 animate-caret transition-all"
                    x-text="text">Siap Kerja</span>
            </div>
            <div class="pt-3 border-t border-white/15 max-w-xs mx-auto opacity-90">
                <p class="text-[9px] sm:text-xs text-amber-300 font-mono font-bold uppercase tracking-widest">
                    Kementerian Ketenagakerjaan RI</p>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings?->sliders && count($settings->sliders) > 1): ?>
        
        <div class="absolute bottom-4 sm:bottom-10 left-0 right-0 flex justify-center gap-3 z-30">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $settings->sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button @click="currentSlider = <?php echo e($index); ?>"
                    class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                    :class="currentSlider === <?php echo e($index); ?> ? 'w-8 bg-amber-400' : 'w-2 bg-white/30 hover:bg-white/60'"></button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/components/home/hero.blade.php ENDPATH**/ ?>