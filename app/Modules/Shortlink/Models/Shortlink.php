<?php

namespace App\Modules\Shortlink\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'custom_title',
        'custom_description',
        'custom_button_text',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_capture_active' => 'boolean',
        'is_active'         => 'boolean',
        'capture_fields'    => 'array',
        'clicks_count'      => 'integer',
    ];

    protected function casts(): array
    {
        return [
            'is_capture_active' => 'boolean',
            'is_active'         => 'boolean',
            'capture_fields'    => 'array',
            'clicks_count'      => 'integer',
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

    /**
     * Get the display title with fallback default
     */
    public function getDisplayTitleAttribute(): string
    {
        return !empty(trim($this->custom_title ?? ''))
            ? $this->custom_title
            : 'Selamat Datang!';
    }

    /**
     * Get the display description with fallback default
     */
    public function getDisplayDescriptionAttribute(): string
    {
        return !empty(trim($this->custom_description ?? ''))
            ? $this->custom_description
            : 'Silakan lengkapi informasi singkat di bawah ini sebelum melanjutkan ke tautan tujuan.';
    }

    /**
     * Get the display button text with fallback default
     */
    public function getDisplayButtonTextAttribute(): string
    {
        return !empty(trim($this->custom_button_text ?? ''))
            ? $this->custom_button_text
            : 'Lanjutkan ke Tautan';
    }
}
