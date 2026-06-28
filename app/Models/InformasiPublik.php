<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // 1. WAJIB IMPORT FACADE STORAGE

class InformasiPublik extends Model
{
    protected $fillable = [
        'kategori',
        'nama_dokumen',
        'deskripsi',
        'file_path',
    ];

    /**
     * Boot function untuk menangani Model Events
     */
    protected static function booted(): void
    {
        // 2. TRIGGER OTOMATIS SAAT DATA BERHASIL DIHAPUS
        static::deleted(function (InformasiPublik $record) {
            // Cek apakah kolom file_path ada isinya dan file-nya benar-benar ada di disk public
            if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                // Hapus file fisik dari storage
                Storage::disk('public')->delete($record->file_path);
            }
        });
    }
}
