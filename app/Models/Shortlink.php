<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Shortlink extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_name',
        'code',
        'destination_url',
        'clicks_count',
        'is_capture_active',
        'capture_fields',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_capture_active' => 'boolean',
        'is_active' => 'boolean',
        'capture_fields' => 'array',
        'clicks_count' => 'integer',
    ];

    protected function casts(): array
    {
        return [
            'is_capture_active' => 'boolean',
            'is_active' => 'boolean',
            'capture_fields' => 'array',
            'clicks_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(ShortlinkLead::class);
    }

    /**
     * Generate unique 5-character alphanumeric code
     */
    public static function generateUniqueCode(int $length = 5): string
    {
        $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $charLength = strlen($characters);

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $charLength - 1)];
            }
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get the full public URL for this shortlink
     */
    public function getShortUrlAttribute(): string
    {
        return url('/s/' . $this->code);
    }
}
