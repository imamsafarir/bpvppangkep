<?php

namespace App\Modules\TimSosmed\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function contents()
    {
        return $this->belongsToMany(Content::class);
    }
}
