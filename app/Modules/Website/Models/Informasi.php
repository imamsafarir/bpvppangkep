<?php

namespace App\Modules\Website\Models;

use App\Services\ImageCompressor;
use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    protected $table = 'informasis';

    protected $fillable = [
        'kejuruan',
        'gedung_fasilitas',
        'kelas_workshop',
        'alumni',
        'testimoni',
        'kerjasama',
        'faq',
    ];

    protected $casts = [
        'kejuruan'         => 'array',
        'gedung_fasilitas' => 'array',
        'kelas_workshop'   => 'array',
        'alumni'           => 'array',
        'testimoni'        => 'array',
        'kerjasama'        => 'array',
        'faq'              => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(function (Informasi $record) {
            // Mapping: nama field JSON => key gambar di dalam tiap item
            $repeaterImageMap = [
                'kejuruan'         => ['foto_kejuruan'],
                'gedung_fasilitas' => ['foto_fasilitas'],
                'kelas_workshop'   => ['foto_ruangan'],
                'alumni'           => ['foto_kegiatan_alumni'],
                'testimoni'        => ['foto_alumni'],
                'kerjasama'        => ['logo'],
            ];

            foreach ($repeaterImageMap as $field => $imageKeys) {
                $items = (array) ($record->$field ?? []);
                foreach ($items as $item) {
                    foreach ($imageKeys as $key) {
                        if (! empty($item[$key])) {
                            ImageCompressor::compressPublic($item[$key]);
                        }
                    }
                }
            }
        });
    }
}
