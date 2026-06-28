<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // 💡 Import Facade Storage agar lebih bersih

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
        'tags' => 'array', // Menyimpan banyak kategori/tags berita
        'file_foto' => 'array', // Mendukung single string untuk berita ATAU multiple array untuk galeri
    ];

    protected static function booted(): void
    {
        // 💡 PERBAIKAN: Menggunakan 'deleting' dan menambahkan perulangan (foreach) untuk menghapus array file
        static::deleting(function (BeritaDanGaleri $record) {
            $files = $record->file_foto ?? [];

            foreach ((array) $files as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        });
    }
}
