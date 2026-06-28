<div class="mt-2 mb-6">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($url): ?>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">🖥️ <?php echo e($label); ?> :</p>
        <div class="w-full overflow-hidden rounded-xl border border-gray-300 dark:border-gray-700 shadow-inner bg-white">
            <iframe src="<?php echo e($url); ?>" width="100%" height="550" frameborder="0" marginheight="0" marginwidth="0"
                class="w-full" style="border: none;">
                Memuat Google Form...
            </iframe>
        </div>
    <?php else: ?>
        <div
            class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-700 text-sm text-gray-400 text-center">
            ⚠️ Belum ada tautan Google Form yang disimpan. Sediakan link di atas untuk memunculkan form.
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\HP\Herd\bpvppangkep\resources\views/filament/pages/gform-embed.blade.php ENDPATH**/ ?>