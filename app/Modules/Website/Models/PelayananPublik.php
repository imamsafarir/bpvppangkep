<?php

namespace App\Modules\Website\Models;

use Illuminate\Database\Eloquent\Model;

class PelayananPublik extends Model
{
    protected $table = 'pelayanan_publiks';

    protected $fillable = [
        'maklumat_pelayanan',
        'standar_pelayanan',
        'foto_alur_pelayanan',
        'deskripsi_alur_pelayanan',
        'survey_kepuasan_masyarakat',
        'survey_kebutuhan_pelatihan',
        'survey_kebekerjaan',
        'indeks_kepuasan_masyarakat',
    ];

    protected $casts = [
        'maklumat_pelayanan' => 'array',
        'standar_pelayanan'  => 'array',
        'alur_pelayanan'     => 'array',
    ];
}
