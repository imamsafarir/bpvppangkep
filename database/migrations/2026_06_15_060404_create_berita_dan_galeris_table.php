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
        Schema::create('berita_dan_galeris', function (Blueprint $table) {
            $table->id();
            $table->string('jenis'); // Isinya: 'berita' atau 'galeri'

            // Kolom Berita
            $table->string('judul_berita')->nullable();
            $table->text('tags')->nullable(); // 1. TAMBAHKAN DI SINI (Menggunakan text agar muat JSON Array)
            $table->text('konten_berita')->nullable();

            // Kolom Bersama (Foto Sampul Berita / Foto Kegiatan Galeri)
            // 2. UBAH ke text agar muat menampung string JSON dari banyak foto galeri
            $table->text('file_foto')->nullable();

            // Kolom Tambahan Galeri
            $table->string('keterangan_galeri')->nullable(); // Caption foto galeri
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_dan_galeris');
    }
};
