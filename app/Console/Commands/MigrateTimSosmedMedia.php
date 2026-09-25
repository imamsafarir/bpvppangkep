<?php

namespace App\Console\Commands;

use App\Services\ImageCompressor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Artisan command: php artisan timsosmed:migrate-media
 *
 * Tugas:
 * 1. Scan semua record di tabel 'media' yang model_type = Content
 * 2. Pindahkan file fisik asli dari content/{id}/... ke timsosmed/content/{id}/...
 * 3. Pindahkan file conversion preview (avif/thumb) ke folder baru
 * 4. Regenerasi / pastikan preview AVIF tersedia untuk semua media gambar
 */
class MigrateTimSosmedMedia extends Command
{
    protected $signature   = 'timsosmed:migrate-media';
    protected $description = 'Migrasi file media TimSosmed ke timsosmed/content/ dan pastikan preview AVIF tersedia';

    private int $moved   = 0;
    private int $converted = 0;
    private int $skipped = 0;
    private int $errors  = 0;

    public function handle(): int
    {
        $this->info('🚀 Memulai sinkronisasi seluruh media TimSosmed ke folder timsosmed/content/ ...');

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
        $this->info('✅ Sinkronisasi Media TimSosmed Selesai!');
        $this->table(
            ['Dipindahkan', 'Preview AVIF Dibuat', 'Dilewati', 'Error'],
            [[$this->moved, $this->converted, $this->skipped, $this->errors]]
        );

        return self::SUCCESS;
    }

    private function migrateMediaRecord(object $media): void
    {
        $id         = $media->model_id;
        $collection = $media->collection_name;
        $fileName   = $media->file_name;
        $disk       = Storage::disk('public');

        $oldPath    = "content/{$id}/{$collection}/{$fileName}";
        $newPath    = "timsosmed/content/{$id}/{$collection}/{$fileName}";

        // 1. Pindahkan file utama jika masih di lokasi lama
        if ($disk->exists($oldPath) && ! $disk->exists($newPath)) {
            $newDir = dirname($newPath);
            if (! $disk->directoryExists($newDir)) {
                $disk->makeDirectory($newDir);
            }
            $disk->put($newPath, $disk->get($oldPath));
            $disk->delete($oldPath);
            $this->moved++;
        } elseif ($disk->exists($newPath)) {
            $this->skipped++;
            if ($disk->exists($oldPath)) {
                $disk->delete($oldPath);
            }
        } else {
            $this->errors++;
        }

        // 2. Pindahkan folder conversions jika masih di lokasi lama
        $oldConvDir = "content/{$id}/{$collection}/conversions";
        $newConvDir = "timsosmed/content/{$id}/{$collection}/conversions";

        if ($disk->directoryExists($oldConvDir)) {
            if (! $disk->directoryExists($newConvDir)) {
                $disk->makeDirectory($newConvDir);
            }
            foreach ($disk->files($oldConvDir) as $convFile) {
                $convName = basename($convFile);
                $disk->put("{$newConvDir}/{$convName}", $disk->get($convFile));
                $disk->delete($convFile);
            }
            try {
                $disk->deleteDirectory($oldConvDir);
            } catch (\Throwable) {
            }
        }

        // 3. Pastikan preview AVIF ada untuk media gambar
        if (str_starts_with($media->mime_type ?? '', 'image/') && $disk->exists($newPath)) {
            $fileBaseName = pathinfo($fileName, PATHINFO_FILENAME);
            $avifPreviewName = "{$fileBaseName}-avif-preview.avif";
            $avifPreviewRelPath = "{$newConvDir}/{$avifPreviewName}";

            if (! $disk->exists($avifPreviewRelPath)) {
                if (! $disk->directoryExists($newConvDir)) {
                    $disk->makeDirectory($newConvDir);
                }

                // Salin file asli ke lokasi conversions lalu kompres ke AVIF
                $disk->put($avifPreviewRelPath, $disk->get($newPath));
                $success = ImageCompressor::compressPublic($avifPreviewRelPath, 75);

                if ($success) {
                    $this->converted++;
                }
            }
        }

        // Hapus folder induk lama jika sudah kosong
        $oldParentDir = "content/{$id}/{$collection}";
        if ($disk->directoryExists($oldParentDir) && empty($disk->files($oldParentDir))) {
            try {
                $disk->deleteDirectory($oldParentDir);
            } catch (\Throwable) {
            }
        }
    }
}
