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
<section id="about" class="bg-slate-100/70 py-20 scroll-mt-20 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-7 space-y-6">
            
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight min-h-[5.5rem] sm:min-h-[7rem]"
                x-data="{
                    aboutText: '',
                    aboutWords: ['Melalui Keterbukaan.', 'Dengan Transparansi.', 'Demi Akuntabilitas.'],
                    wordIndex: 0,
                    charIndex: 0,
                    isDeleting: false,
                    typeEffect() {
                        let currentWord = this.aboutWords[this.wordIndex];
                        if (this.isDeleting) {
                            this.aboutText = currentWord.substring(0, this.charIndex - 1);
                            this.charIndex--;
                        } else {
                            this.aboutText = currentWord.substring(0, this.charIndex + 1);
                            this.charIndex++;
                        }

                        let typeSpeed = this.isDeleting ? 30 : 60;

                        if (!this.isDeleting && this.charIndex === currentWord.length) {
                            typeSpeed = 2500; // Berhenti lama saat kalimat lengkap
                            this.isDeleting = true;
                        } else if (this.isDeleting && this.aboutText === '') {
                            this.isDeleting = false;
                            this.wordIndex = (this.wordIndex + 1) % this.aboutWords.length;
                            typeSpeed = 300;
                        }

                        setTimeout(() => this.typeEffect(), typeSpeed);
                    }
                }" x-init="setTimeout(() => typeEffect(), 1500)">

                <span class="block">Membangun Kepercayaan,</span>

                <span class="text-blue-600 block sm:inline-block border-r-3 border-blue-600 pr-1 animate-caret"
                    x-text="aboutText"></span>
            </h1>
            <p class="text-slate-600 text-sm sm:text-base font-normal leading-relaxed max-w-2xl">
                Kami menyajikan informasi publik secara transparan, akurat, dan dapat dipertanggungjawabkan kepada
                seluruh lapisan masyarakat sebagai wujud pelaksanaan reformasi birokrasi di lingkungan kerja Balai
                Pelatihan.
            </p>
            <div class="pt-2 flex flex-wrap gap-4">
                <a href="#documents"
                    class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-6 py-3.5 rounded-xl shadow-lg shadow-blue-600/10 transition-all">
                    Lihat Informasi Publik
                </a>
                <a href="https://api.whatsapp.com/send?phone=<?php echo e($settings?->whatsapp_number ?? '6285343747243'); ?>&text=Halo%20PPID%20BPVP%20Pangkep..."
                    target="_blank"
                    class="border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold px-6 py-3.5 rounded-xl transition-all">
                    Permohonan Informasi via WA
                </a>
            </div>
        </div>
        <div
            class="lg:col-span-5 rounded-2xl overflow-hidden shadow-xl border border-slate-200 aspect-4/3 min-h-[300px] bg-slate-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings?->google_maps_embed): ?>
                <?php echo $settings->google_maps_embed; ?>

            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-xs text-slate-400 p-4 text-center">
                    Gunakan menu Pengaturan di panel admin untuk menampilkan peta navigasi Google Maps instansi di
                    sini.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/components/home/about.blade.php ENDPATH**/ ?>