<?php

namespace App\Modules\Website\Models;

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
        static::deleting(function (BeritaDanGaleri $record) {
            foreach ((array) ($record->file_foto ?? []) as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        });
    }
}
