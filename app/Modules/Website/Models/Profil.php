<?php

namespace App\Modules\Website\Models;

use App\Services\ImageCompressor;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $fillable = [
        'chief_name',
        'chief_nip',
        'chief_photo_path',
        'sambutan_kepala',
        'tentang_kami',
        'ppid',
        'tugas_fungsi',
        'visi_misi',
        'struktur_organisasi',
        'pejabat_struktural',
    ];

    protected $casts = ['pejabat_struktural' => 'array'];

    protected static function booted(): void
    {
        static::saved(function (Profil $record) {
            // Compress foto tunggal kepala balai
            if (! empty($record->chief_photo_path)) {
                ImageCompressor::compressPublic($record->chief_photo_path);
            }

            // Compress foto struktur organisasi
            if (! empty($record->struktur_organisasi)) {
                ImageCompressor::compressPublic($record->struktur_organisasi);
            }

            // Compress foto setiap pejabat struktural di dalam JSON array
            $pejabat = (array) ($record->pejabat_struktural ?? []);
            foreach ($pejabat as $item) {
                if (! empty($item['foto'])) {
                    ImageCompressor::compressPublic($item['foto']);
                }
            }
        });
    }
}
