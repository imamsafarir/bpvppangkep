<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $table = 'website_settings';

    protected $fillable = [
        // Identitas & Multi Sliders
        'website_name',
        'logo_path',
        'favicon_path',
        'sliders',

        // Kontak Instansi
        'email',
        'whatsapp_number',
        'phone_number',
        'address',
        'google_maps_embed',

        // Sosial Media
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',

        // Iklan Banner & Running Text
        'is_popup_active',
        'popup_image_path',
        'popup_redirect_url',
        'is_running_text_active',
        'running_text_content',
    ];

    protected $casts = [
        'is_popup_active' => 'boolean',
        'is_running_text_active' => 'boolean',
        'sliders' => 'array', // WAJIB: Otomatis konversi Array <-> JSON
    ];
}
