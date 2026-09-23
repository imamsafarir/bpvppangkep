<?php

namespace App\Modules\Website\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InformasiPublik extends Model
{
    protected $fillable = ['kategori', 'nama_dokumen', 'deskripsi', 'file_path'];

    protected static function booted(): void
    {
        static::deleted(function (InformasiPublik $record) {
            if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                Storage::disk('public')->delete($record->file_path);
            }
        });
    }
}
