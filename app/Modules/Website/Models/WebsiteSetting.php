<?php

namespace App\Modules\Website\Models;

use App\Services\ImageCompressor;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $table = 'website_settings';

    protected $fillable = [
        'website_name',
        'logo_path',
        'favicon_path',
        'sliders',
        'email',
        'whatsapp_number',
        'phone_number',
        'address',
        'google_maps_embed',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',
        'is_popup_active',
        'popup_image_path',
        'popup_redirect_url',
        'is_running_text_active',
        'running_text_content',
    ];

    protected $casts = [
        'is_popup_active'        => 'boolean',
        'is_running_text_active' => 'boolean',
        'sliders'                => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(function (WebsiteSetting $record) {
            // Logo — compress ke AVIF
            if (! empty($record->logo_path)) {
                ImageCompressor::compressPublic($record->logo_path);
            }

            // Popup image — compress ke AVIF
            if (! empty($record->popup_image_path)) {
                ImageCompressor::compressPublic($record->popup_image_path);
            }

            // Favicon — skip jika .ico (browser tidak support ico via GD)
            if (! empty($record->favicon_path)) {
                $ext = strtolower(pathinfo($record->favicon_path, PATHINFO_EXTENSION));
                if ($ext !== 'ico') {
                    ImageCompressor::compressPublic($record->favicon_path);
                }
            }

            // Compress setiap gambar slider
            $sliders = (array) ($record->sliders ?? []);
            foreach ($sliders as $sliderPath) {
                if (! empty($sliderPath)) {
                    ImageCompressor::compressPublic($sliderPath);
                }
            }
        });
    }
}
