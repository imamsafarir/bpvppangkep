<?php

namespace App\Modules\Website\Models;

use App\Services\ImageCompressor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BeritaDanGaleri extends Model
{
    protected $table = 'berita_dan_galeris';

    protected $fillable = [
        'jenis',
        'judul_berita',
        'konten_berita',
        'tags',
        'file_foto',
        'keterangan_galeri',
    ];

    protected $casts = [
        'tags'      => 'array',
        'file_foto' => 'array',
    ];

    protected static function booted(): void
    {
        // Compress semua foto ke AVIF setelah record disimpan
        static::saved(function (BeritaDanGaleri $record) {
            $fotos = (array) ($record->file_foto ?? []);
            if (empty($fotos)) {
                return;
            }

            foreach ($fotos as $relativePath) {
                if (! empty($relativePath)) {
                    ImageCompressor::compressPublic($relativePath);
                }
            }
        });

        // Bersihkan file fisik dari storage saat record dihapus
        static::deleting(function (BeritaDanGaleri $record) {
            foreach ((array) ($record->file_foto ?? []) as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        });
    }
}
