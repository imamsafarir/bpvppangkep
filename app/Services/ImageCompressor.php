<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * ImageCompressor
 *
 * Service untuk mengompresi dan mengkonversi gambar ke format AVIF
 * menggunakan ekstensi GD bawaan PHP (tanpa library tambahan).
 *
 * Membutuhkan: PHP >= 8.1 + GD dengan AVIF support (tersedia di PHP 8.1+).
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
     * @param  int     $quality       Kualitas AVIF (0–100, default 72)
     * @return bool    true = berhasil dikonversi, false = dilewati / gagal
     */
    public static function compress(string $absolutePath, int $quality = 72): bool
    {
        if (! file_exists($absolutePath) || ! is_readable($absolutePath)) {
            return false;
        }

        // Deteksi mime type secara andal
        $mime = self::detectMime($absolutePath);

        if (! in_array($mime, self::SUPPORTED_MIMES, true)) {
            return false;
        }

        // Jika file sudah AVIF dan berukuran kecil, lewati
        if ($mime === 'image/avif') {
            return true;
        }

        // Buat image resource dari GD
        $image = self::createImageResource($absolutePath, $mime);

        if ($image === null) {
            return false;
        }

        // Preserve transparansi untuk PNG
        if ($mime === 'image/png') {
            $bgCanvas = imagecreatetruecolor(imagesx($image), imagesy($image));
            imagefill($bgCanvas, 0, 0, imagecolorallocate($bgCanvas, 255, 255, 255));
            imagecopy($bgCanvas, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagedestroy($image);
            $image = $bgCanvas;
        }

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
    public static function compressPublic(string $relativePath, int $quality = 72): bool
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
     * Buat GD image resource berdasarkan MIME type.
     */
    private static function createImageResource(string $path, string $mime): ?\GdImage
    {
        return match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png'               => @imagecreatefrompng($path),
            'image/webp'              => @imagecreatefromwebp($path),
            'image/gif'               => @imagecreatefromgif($path),
            'image/bmp'               => @imagecreatefrombmp($path),
            'image/avif'              => @imagecreatefromavif($path),
            default                   => null,
        };
    }

    /**
     * Batch compress: kompres beberapa file sekaligus (array path relatif).
     *
     * @param  array<string>  $relativePaths
     * @param  int            $quality
     * @return array{compressed: int, skipped: int, failed: int}
     */
    public static function batchCompressPublic(array $relativePaths, int $quality = 72): array
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
