<?php

namespace App\Modules\TimSosmed\Models;

use Illuminate\Database\Eloquent\Model;

class SocialSetting extends Model
{
    //
    protected $fillable = [
        'provider_name',
        'access_token',
        'ig_user_id',
    ];
}
