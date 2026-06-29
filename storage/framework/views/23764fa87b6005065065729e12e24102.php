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

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings?->is_popup_active && $settings->popup_image_path): ?>
    <div x-data="{
        showPopup: true,
        timeLeft: 10,
        timer: null,
        startTimer() {
            this.timer = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    this.closePopup();
                }
            }, 1000);
        },
        closePopup() {
            this.showPopup = false;
            clearInterval(this.timer);
        }
    }" x-init="startTimer()" x-show="showPopup" x-transition.opacity.duration.1000ms
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm transition-opacity duration-300">

        <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden max-w-md w-full relative shadow-2xl border border-slate-100/80 dark:border-slate-800 flex flex-col"
            @click.away="closePopup()">

            
            <button @click="closePopup()"
                class="absolute top-4 right-4 bg-black/40 hover:bg-black/70 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm transition shadow-md z-20">
                ✕
            </button>

            
            <div class="w-full overflow-hidden">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings->popup_redirect_url): ?>
                    <a href="<?php echo e($settings->popup_redirect_url); ?>" target="_blank" class="block">
                        <img src="<?php echo e(asset('storage/' . $settings->popup_image_path)); ?>" alt="Iklan Pengumuman"
                            class="w-full h-auto object-cover max-h-[65vh]">
                    </a>
                <?php else: ?>
                    <img src="<?php echo e(asset('storage/' . $settings->popup_image_path)); ?>" alt="Iklan Pengumuman"
                        class="w-full h-auto object-cover max-h-[65vh]">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div
                class="bg-slate-50 dark:bg-slate-950/40 px-4 py-3.5 border-t border-slate-100 dark:border-slate-800 text-center select-none flex-shrink-0">
                <p class="text-xs font-bold text-slate-500 dark:text-zinc-400 tracking-wide">
                    Otomatis tertutup dalam <span x-text="timeLeft"
                        class="text-amber-500 dark:text-amber-400 text-sm font-black mx-1"></span> detik
                </p>
            </div>

        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/components/home/popup.blade.php ENDPATH**/ ?>