<?php

namespace App\Modules\Website\Models;

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
}
