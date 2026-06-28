<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'kunjungans';
    protected $fillable = ['ip_address', 'tanggal'];
}
