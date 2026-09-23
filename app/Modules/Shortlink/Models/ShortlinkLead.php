<?php

namespace App\Modules\Shortlink\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortlinkLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'shortlink_id',
        'nama',
        'whatsapp',
        'email',
        'ip_address',
        'user_agent',
    ];

    public function shortlink(): BelongsTo
    {
        return $this->belongsTo(Shortlink::class);
    }
}
