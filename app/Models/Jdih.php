<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Import Facade Storage

class Jdih extends Model
{
    protected $table = 'jdihs';

    protected $fillable = [
        'status_peraturan',
        'judul_peraturan',
        'nomor_peraturan',
        'file_path',
        'tentang',
    ];

    /**
     * Otomatisasi Penghapusan Berkas Fisik JDIH dari Disk Storage
     */
    protected static function booted(): void
    {
        static::deleted(function (Jdih $record) {
            if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                Storage::disk('public')->delete($record->file_path);
            }
        });
    }
}
