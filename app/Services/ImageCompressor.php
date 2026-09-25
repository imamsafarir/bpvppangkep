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

        // Deteksi mime type secara andal
        $mime = self::detectMime($absolutePath);

        if (! in_array($mime, self::SUPPORTED_MIMES, true)) {
            return false;
        }

        // Jika file sudah AVIF, tidak perlu dikonversi ulang
        if ($mime === 'image/avif') {
            return true;
        }

        // Buat image resource dari GD dengan preservasi alpha channel penuh
        $image = self::createImageResource($absolutePath, $mime);

        if ($image === null) {
            return false;
        }

        // Pastikan transparansi tetap aktif dan disimpan ke hasil AVIF
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Tulis langsung ke path yang sama (menimpa file lama)
        $result = imageavif($image, $absolutePath, $quality);

        imagedestroy($image);

        return $result;
    }

    /**
     * Kompres gambar di storage publik berdasarkan path relatif
     * (relatif terhadap disk 'public').
     *
     * @param  string  $relativePath  Path relatif di disk public (misal: 'website/berita/sampul/foto.avif')
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

        // Fallback: getimagesize
        $info = @getimagesize($path);

        return $info['mime'] ?? '';
    }

    /**
     * Buat GD image resource berdasarkan MIME type dengan penanganan transparansi yang benar.
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
            // Aktifkan alpha blending & save alpha untuk format yang mendukung transparansi
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        return $image ?: null;
    }

    /**
     * Batch compress: kompres beberapa file sekaligus (array path relatif).
     *
     * @param  array<string>  $relativePaths
     * @param  int            $quality
     * @return array{compressed: int, skipped: int, failed: int}
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
