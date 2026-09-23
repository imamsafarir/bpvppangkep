<?php

namespace App\Modules\Website\Models;

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
}
