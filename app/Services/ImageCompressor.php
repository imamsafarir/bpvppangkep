<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * ImageCompressor
 *
 * Service untuk mengompresi dan mengkonversi gambar ke format AVIF
 * menggunakan ekstensi GD bawaan PHP (tanpa library tambahan).
 *
 * Mendukung transparansi penuh (alpha channel) untuk PNG, WebP, dan GIF.
 */
class ImageCompressor
{
    /**
     * Format MIME yang didukung untuk dikonversi ke AVIF.
     */
    private const SUPPORTED_MIMES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/bmp',
        'image/avif',
    ];

    /**
     * Kompres gambar di path absolut menjadi AVIF.
     * Menimpa file di path yang sama.
     *
     * @param  string  $absolutePath  Path fisik absolut file gambar
     * @param  int     $quality       Kualitas AVIF (0–100, default 75)
     * @return bool    true = berhasil dikonversi, false = dilewati / gagal
     */
    public static function compress(string $absolutePath, int $quality = 75): bool
    {
        if (! file_exists($absolutePath) || ! is_readable($absolutePath)) {
            return false;
        }

        $mime = self::detectMime($absolutePath);

        if (! in_array($mime, self::SUPPORTED_MIMES, true)) {
            return false;
        }

        if ($mime === 'image/avif') {
            return true;
        }

        $image = self::createImageResource($absolutePath, $mime);

        if ($image === null) {
            return false;
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        $result = imageavif($image, $absolutePath, $quality);

        imagedestroy($image);

        return $result;
    }

    /**
     * Kompres dan ubah nama file ke ekstensi .avif.
     * Jika file fisik belum berakhiran .avif, konversi ke file baru .avif dan hapus file lama.
     * Mengembalikan relative path baru (misal: 'website/berita/sampul/foto.avif') atau path lama jika gagal.
     *
     * @param  string  $relativePath  Path relatif di disk public
     * @param  int     $quality
     * @return string  Relative path hasil (bisa berakhiran .avif)
     */
    public static function convertToAvifPublic(string $relativePath, int $quality = 75): string
    {
        if (empty($relativePath)) {
            return $relativePath;
        }

        $relativePath = str_replace('\\', '/', $relativePath);

        // Abaikan file non-gambar
        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        if (in_array($ext, ['ico', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'mp4', 'mov', 'webm'])) {
            return $relativePath;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($relativePath)) {
            return $relativePath;
        }

        $absolutePath = $disk->path($relativePath);
        $mime = self::detectMime($absolutePath);

        if (! in_array($mime, self::SUPPORTED_MIMES, true)) {
            return $relativePath;
        }

        // Tentukan target path yang berakhiran .avif
        $dir = pathinfo($relativePath, PATHINFO_DIRNAME);
        $filenameWithoutExt = pathinfo($relativePath, PATHINFO_FILENAME);
        $newRelativePath = ($dir === '.' ? '' : $dir . '/') . $filenameWithoutExt . '.avif';
        $newAbsolutePath = $disk->path($newRelativePath);

        // Jika file asli sudah .avif dan kontennya memang avif
        if ($ext === 'avif' && $mime === 'image/avif') {
            return $relativePath;
        }

        $image = self::createImageResource($absolutePath, $mime);
        if ($image === null) {
            return $relativePath;
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Pastikan direktori tujuan ada
        $targetDir = dirname($newAbsolutePath);
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $success = imageavif($image, $newAbsolutePath, $quality);
        imagedestroy($image);

        if ($success) {
            // Hapus file lama jika nama file berbeda
            if ($newRelativePath !== $relativePath && $disk->exists($relativePath)) {
                $disk->delete($relativePath);
            }

            return $newRelativePath;
        }

        return $relativePath;
    }

    /**
     * Kompres gambar di storage publik berdasarkan path relatif.
     *
     * @param  string  $relativePath  Path relatif di disk public
     * @param  int     $quality
     * @return bool
     */
    public static function compressPublic(string $relativePath, int $quality = 75): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        $absolutePath = Storage::disk('public')->path($relativePath);

        return self::compress($absolutePath, $quality);
    }

    /**
     * Deteksi MIME type menggunakan finfo (lebih akurat dari ekstensi).
     */
    private static function detectMime(string $path): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $path);
            finfo_close($finfo);

            return $mime ?: '';
        }

        $info = @getimagesize($path);

        return $info['mime'] ?? '';
    }

    /**
     * Buat GD image resource berdasarkan MIME type dengan penanganan transparansi.
     */
    private static function createImageResource(string $path, string $mime): ?\GdImage
    {
        $image = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png'               => @imagecreatefrompng($path),
            'image/webp'              => @imagecreatefromwebp($path),
            'image/gif'               => @imagecreatefromgif($path),
            'image/bmp'               => @imagecreatefrombmp($path),
            'image/avif'              => @imagecreatefromavif($path),
            default                   => null,
        };

        if ($image) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        return $image ?: null;
    }

    /**
     * Batch compress: kompres beberapa file sekaligus (array path relatif).
     */
    public static function batchCompressPublic(array $relativePaths, int $quality = 75): array
    {
        $stats = ['compressed' => 0, 'skipped' => 0, 'failed' => 0];

        foreach ($relativePaths as $path) {
            if (empty($path)) {
                $stats['skipped']++;
                continue;
            }

            $result = self::compressPublic($path, $quality);

            if ($result) {
                $stats['compressed']++;
            } else {
                $stats['failed']++;
            }
        }

        return $stats;
    }
}
