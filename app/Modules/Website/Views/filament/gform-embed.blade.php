<div class="mt-2 mb-6">
    @if ($url)
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">🖥️ {{ $label }} :</p>
        <div class="w-full overflow-hidden rounded-xl border border-gray-300 dark:border-gray-700 shadow-inner bg-white">
            <iframe src="{{ $url }}" width="100%" height="550" frameborder="0" marginheight="0" marginwidth="0"
                class="w-full" style="border: none;">
                Memuat Google Form...
            </iframe>
        </div>
    @else
        <div
            class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-700 text-sm text-gray-400 text-center">
            ⚠️ Belum ada tautan Google Form yang disimpan. Sediakan link di atas untuk memunculkan form.
        </div>
    @endif
</div>
