<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Artisan command: php artisan timsosmed:migrate-media
 *
 * Tugas:
 * 1. Scan semua record di tabel 'media' yang model_type = Content dan path lama (content/{id}/...)
 * 2. Pindahkan file fisiknya ke timsosmed/content/{id}/...
 * 3. Update kolom 'custom_properties' dan path terkait di tabel media
 */
class MigrateTimSosmedMedia extends Command
{
    protected $signature   = 'timsosmed:migrate-media';
    protected $description = 'Migrasi file media TimSosmed dari content/ ke timsosmed/content/';

    private int $moved   = 0;
    private int $skipped = 0;
    private int $errors  = 0;

    public function handle(): int
    {
        $this->info('🚀 Memulai migrasi media TimSosmed ke folder timsosmed/content/ ...');

        // Ambil semua media milik Content yang masih di path lama
        $mediaRows = DB::table('media')
            ->where('model_type', 'App\\Modules\\TimSosmed\\Models\\Content')
            ->get();

        if ($mediaRows->isEmpty()) {
            $this->warn('Tidak ada media yang perlu dimigrasi.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$mediaRows->count()} record media...");
        $bar = $this->output->createProgressBar($mediaRows->count());
        $bar->start();

        foreach ($mediaRows as $media) {
            $this->migrateMediaRecord($media);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Selesai!");
        $this->table(
            ['Dipindahkan', 'Dilewati', 'Error'],
            [[$this->moved, $this->skipped, $this->errors]]
        );

        return self::SUCCESS;
    }

    private function migrateMediaRecord(object $media): void
    {
        $id         = $media->model_id;
        $collection = $media->collection_name;
        $uuid       = $media->uuid;
        $fileName   = $media->file_name;

        // Path lama (sebelum prefix timsosmed/)
        $oldPath    = "content/{$id}/{$collection}/{$fileName}";
        // Path baru
        $newPath    = "timsosmed/content/{$id}/{$collection}/{$fileName}";

        // Cek apakah sudah di lokasi baru
        if (Storage::disk('public')->exists($newPath)) {
            $this->skipped++;
            // Hapus file lama jika masih ada
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            return;
        }

        // Cek file lama ada
        if (! Storage::disk('public')->exists($oldPath)) {
            $this->skipped++;
            return;
        }

        // Buat direktori tujuan
        $newDir = "timsosmed/content/{$id}/{$collection}";
        if (! Storage::disk('public')->directoryExists($newDir)) {
            Storage::disk('public')->makeDirectory($newDir);
        }

        // Salin file utama
        $contents = Storage::disk('public')->get($oldPath);
        Storage::disk('public')->put($newPath, $contents);
        Storage::disk('public')->delete($oldPath);
        $this->moved++;

        // Pindahkan conversions (misal AVIF preview)
        $oldConvDir = "content/{$id}/{$collection}/conversions";
        $newConvDir = "timsosmed/content/{$id}/{$collection}/conversions";

        if (Storage::disk('public')->directoryExists($oldConvDir)) {
            $convFiles = Storage::disk('public')->files($oldConvDir);
            if (! Storage::disk('public')->directoryExists($newConvDir)) {
                Storage::disk('public')->makeDirectory($newConvDir);
            }
            foreach ($convFiles as $convFile) {
                $convName     = basename($convFile);
                $convContents = Storage::disk('public')->get($convFile);
                Storage::disk('public')->put("{$newConvDir}/{$convName}", $convContents);
                Storage::disk('public')->delete($convFile);
            }

            // Coba hapus folder conversions lama jika sudah kosong
            try {
                Storage::disk('public')->deleteDirectory($oldConvDir);
            } catch (\Throwable) {
            }
        }

        // Coba hapus folder lama jika kosong
        $oldModelDir = "content/{$id}/{$collection}";
        try {
            $remaining = Storage::disk('public')->files($oldModelDir);
            if (empty($remaining)) {
                Storage::disk('public')->deleteDirectory($oldModelDir);
            }
        } catch (\Throwable) {
        }
    }
}
