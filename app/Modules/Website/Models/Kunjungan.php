<?php

namespace App\Modules\Website\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'kunjungans';
    protected $fillable = ['ip_address', 'tanggal'];
}
