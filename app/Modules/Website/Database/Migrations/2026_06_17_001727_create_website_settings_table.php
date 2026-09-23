<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();

            // Baru: Identitas Umum Website
            $table->string('website_name')->default('PPID BPVP Pangkep');
            $table->string('logo_path')->nullable();    // Untuk Icon / Logo Utama Navbar
            $table->string('favicon_path')->nullable(); // Untuk Favicon Tab Browser
            $table->json('sliders')->nullable();        // Baru: Tempat menampung banyak gambar slider (Array JSON)

            // 1. Kontak & Informasi Resmi
            $table->string('email')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->text('google_maps_embed')->nullable();

            // 2. Alamat Sosial Media
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('tiktok_url')->nullable();

            // 3. Popup Iklan / Banner Pengumuman
            $table->boolean('is_popup_active')->default(false);
            $table->string('popup_image_path')->nullable();
            $table->string('popup_redirect_url')->nullable();

            // 4. Teks Berjalan (Running Text)
            $table->boolean('is_running_text_active')->default(false);
            $table->text('running_text_content')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
