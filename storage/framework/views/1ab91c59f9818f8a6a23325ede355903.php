<?php $__env->startSection('title', ($settings?->website_name ?? 'BPVP Pangkep') . ' - Beranda'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Efek Kursor Mengetik Berkedip & Animasi Lainnya Tetap Disini */
        @keyframes blink-caret {

            from,
            to {
                border-color: transparent
            }

            50% {
                border-color: #fbbf24;
            }
        }

        @keyframes fade-in-up {
            0% {
                opacity: 0;
                transform: translate3d(0, 30px, 0);
            }

            100% {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes fade-in-down {
            0% {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }

            100% {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes text-shimmer {
            0% {
                background-position: 0% center;
            }

            100% {
                background-position: -200% center;
            }
        }

        .animate-caret {
            animation: blink-caret 0.75s step-end infinite;
        }

        .animate-fade-in-up {
            animation: fade-in-up 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-fade-in-down {
            animation: fade-in-down 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-text-shimmer {
            animation: text-shimmer 4s linear infinite;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="relative w-full overflow-hidden">

        
        <?php if (isset($component)) { $__componentOriginal327220d710845b5b975fddfa8e8dcd7f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal327220d710845b5b975fddfa8e8dcd7f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.home.hero','data' => ['settings' => $settings]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('home.hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['settings' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($settings)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal327220d710845b5b975fddfa8e8dcd7f)): ?>
<?php $attributes = $__attributesOriginal327220d710845b5b975fddfa8e8dcd7f; ?>
<?php unset($__attributesOriginal327220d710845b5b975fddfa8e8dcd7f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal327220d710845b5b975fddfa8e8dcd7f)): ?>
<?php $component = $__componentOriginal327220d710845b5b975fddfa8e8dcd7f; ?>
<?php unset($__componentOriginal327220d710845b5b975fddfa8e8dcd7f); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginala244f833a2dadbaf564ac5bdae9875ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala244f833a2dadbaf564ac5bdae9875ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.home.cta-pelatihan','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('home.cta-pelatihan'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala244f833a2dadbaf564ac5bdae9875ab)): ?>
<?php $attributes = $__attributesOriginala244f833a2dadbaf564ac5bdae9875ab; ?>
<?php unset($__attributesOriginala244f833a2dadbaf564ac5bdae9875ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala244f833a2dadbaf564ac5bdae9875ab)): ?>
<?php $component = $__componentOriginala244f833a2dadbaf564ac5bdae9875ab; ?>
<?php unset($__componentOriginala244f833a2dadbaf564ac5bdae9875ab); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal44d245b7af9a16aaf79cf393f4e1bca7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal44d245b7af9a16aaf79cf393f4e1bca7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.home.berita','data' => ['berita' => $berita_terbaru]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('home.berita'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['berita' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($berita_terbaru)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal44d245b7af9a16aaf79cf393f4e1bca7)): ?>
<?php $attributes = $__attributesOriginal44d245b7af9a16aaf79cf393f4e1bca7; ?>
<?php unset($__attributesOriginal44d245b7af9a16aaf79cf393f4e1bca7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal44d245b7af9a16aaf79cf393f4e1bca7)): ?>
<?php $component = $__componentOriginal44d245b7af9a16aaf79cf393f4e1bca7; ?>
<?php unset($__componentOriginal44d245b7af9a16aaf79cf393f4e1bca7); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalcd8302afe82d9633b3e8202f383074b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcd8302afe82d9633b3e8202f383074b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.home.about','data' => ['settings' => $settings]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('home.about'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['settings' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($settings)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcd8302afe82d9633b3e8202f383074b9)): ?>
<?php $attributes = $__attributesOriginalcd8302afe82d9633b3e8202f383074b9; ?>
<?php unset($__attributesOriginalcd8302afe82d9633b3e8202f383074b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcd8302afe82d9633b3e8202f383074b9)): ?>
<?php $component = $__componentOriginalcd8302afe82d9633b3e8202f383074b9; ?>
<?php unset($__componentOriginalcd8302afe82d9633b3e8202f383074b9); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal369d9c84efc741d4b4d0418df24b69f8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal369d9c84efc741d4b4d0418df24b69f8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.home.statistik','data' => ['totalInformasi' => $total_informasi,'totalJdih' => $total_jdih,'totalBerita' => $total_berita,'totalUnduhan' => $total_unduhan,'totalKunjungan' => $total_kunjungan]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('home.statistik'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['total-informasi' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($total_informasi),'total-jdih' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($total_jdih),'total-berita' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($total_berita),'total-unduhan' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($total_unduhan),'total-kunjungan' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($total_kunjungan)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal369d9c84efc741d4b4d0418df24b69f8)): ?>
<?php $attributes = $__attributesOriginal369d9c84efc741d4b4d0418df24b69f8; ?>
<?php unset($__attributesOriginal369d9c84efc741d4b4d0418df24b69f8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal369d9c84efc741d4b4d0418df24b69f8)): ?>
<?php $component = $__componentOriginal369d9c84efc741d4b4d0418df24b69f8; ?>
<?php unset($__componentOriginal369d9c84efc741d4b4d0418df24b69f8); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal0205db578b728f1186bd111442b3e210 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0205db578b728f1186bd111442b3e210 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.home.documents','data' => ['informasi' => $informasi]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('home.documents'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['informasi' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($informasi)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0205db578b728f1186bd111442b3e210)): ?>
<?php $attributes = $__attributesOriginal0205db578b728f1186bd111442b3e210; ?>
<?php unset($__attributesOriginal0205db578b728f1186bd111442b3e210); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0205db578b728f1186bd111442b3e210)): ?>
<?php $component = $__componentOriginal0205db578b728f1186bd111442b3e210; ?>
<?php unset($__componentOriginal0205db578b728f1186bd111442b3e210); ?>
<?php endif; ?>

        

        <?php if (isset($component)) { $__componentOriginal4030b1daf7a71d10ea941c1ec668764f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4030b1daf7a71d10ea941c1ec668764f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.home.faq','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('home.faq'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4030b1daf7a71d10ea941c1ec668764f)): ?>
<?php $attributes = $__attributesOriginal4030b1daf7a71d10ea941c1ec668764f; ?>
<?php unset($__attributesOriginal4030b1daf7a71d10ea941c1ec668764f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4030b1daf7a71d10ea941c1ec668764f)): ?>
<?php $component = $__componentOriginal4030b1daf7a71d10ea941c1ec668764f; ?>
<?php unset($__componentOriginal4030b1daf7a71d10ea941c1ec668764f); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal4bc6e399f948fbdd4646847f4f76bfb6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bc6e399f948fbdd4646847f4f76bfb6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.home.partners','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('home.partners'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4bc6e399f948fbdd4646847f4f76bfb6)): ?>
<?php $attributes = $__attributesOriginal4bc6e399f948fbdd4646847f4f76bfb6; ?>
<?php unset($__attributesOriginal4bc6e399f948fbdd4646847f4f76bfb6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4bc6e399f948fbdd4646847f4f76bfb6)): ?>
<?php $component = $__componentOriginal4bc6e399f948fbdd4646847f4f76bfb6; ?>
<?php unset($__componentOriginal4bc6e399f948fbdd4646847f4f76bfb6); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalccdd5deb1d41aaa0927fab23759b279a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalccdd5deb1d41aaa0927fab23759b279a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.home.popup','data' => ['settings' => $settings]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('home.popup'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['settings' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($settings)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalccdd5deb1d41aaa0927fab23759b279a)): ?>
<?php $attributes = $__attributesOriginalccdd5deb1d41aaa0927fab23759b279a; ?>
<?php unset($__attributesOriginalccdd5deb1d41aaa0927fab23759b279a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalccdd5deb1d41aaa0927fab23759b279a)): ?>
<?php $component = $__componentOriginalccdd5deb1d41aaa0927fab23759b279a; ?>
<?php unset($__componentOriginalccdd5deb1d41aaa0927fab23759b279a); ?>
<?php endif; ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/home.blade.php ENDPATH**/ ?>