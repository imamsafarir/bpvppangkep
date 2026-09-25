<?php

namespace App\Modules\TimSosmed\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Job untuk mengkompresi video ke format AV1 (WebM).
 *
 * Diperlukan FFmpeg di server.
 * Di lokal (Windows tanpa FFmpeg), job ini otomatis di-skip.
 * File asli SELALU dipertahankan — AV1 disimpan sebagai file tambahan.
 */
class CompressMediaToAv1Job implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 2;

    public int $timeout = 600; // 10 menit

    public function __construct(
        public readonly int $mediaId
    ) {}

    public function handle(): void
    {
        // Cek FFmpeg tersedia
        $ffmpegPath = $this->findFfmpeg();
        if (! $ffmpegPath) {
            Log::info("[CompressMediaToAv1Job] FFmpeg tidak ditemukan, skip kompresi AV1 untuk media #{$this->mediaId}");
            return;
        }

        $media = Media::find($this->mediaId);
        if (! $media) {
            Log::warning("[CompressMediaToAv1Job] Media #{$this->mediaId} tidak ditemukan.");
            return;
        }

        // Hanya proses video
        if (! str_starts_with($media->mime_type, 'video/')) {
            return;
        }

        $originalPath = $media->getPath();
        if (! file_exists($originalPath)) {
            Log::warning("[CompressMediaToAv1Job] File tidak ditemukan: {$originalPath}");
            return;
        }

        // Output AV1 di folder yang sama, suffix _av1.webm
        $outputPath = preg_replace('/\.[^.]+$/', '_av1.webm', $originalPath);

        // Kompresi ke AV1 (codec libaom-av1)
        $cmd = escapeshellcmd($ffmpegPath)
            . ' -i ' . escapeshellarg($originalPath)
            . ' -c:v libaom-av1 -crf 35 -b:v 0'
            . ' -c:a libopus -b:a 128k'
            . ' -cpu-used 4'   // trade-off speed vs quality (0=best, 8=fastest)
            . ' -row-mt 1'
            . ' -y '           // overwrite output jika ada
            . escapeshellarg($outputPath)
            . ' 2>&1';

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            Log::error("[CompressMediaToAv1Job] FFmpeg gagal (exit {$exitCode}): " . implode("\n", array_slice($output, -5)));
            return;
        }

        // Simpan path output ke custom_properties agar bisa diakses di view
        $media->setCustomProperty('av1_path', $outputPath);
        $media->setCustomProperty('av1_size', file_exists($outputPath) ? filesize($outputPath) : null);
        $media->save();

        Log::info("[CompressMediaToAv1Job] AV1 berhasil: {$outputPath}");
    }

    /**
     * Cari executable ffmpeg di PATH atau lokasi umum.
     */
    private function findFfmpeg(): ?string
    {
        // Cek via which/where
        $cmd = PHP_OS_FAMILY === 'Windows' ? 'where ffmpeg 2>nul' : 'which ffmpeg 2>/dev/null';
        $result = trim((string) shell_exec($cmd));
        if (filled($result) && file_exists(explode("\n", $result)[0])) {
            return explode("\n", $result)[0];
        }

        // Cek dari config
        $configured = config('media-library.ffmpeg_path');
        if (filled($configured) && file_exists($configured)) {
            return $configured;
        }

        return null;
    }
}
