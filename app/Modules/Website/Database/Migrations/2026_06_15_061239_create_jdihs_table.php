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
        Schema::create('jdihs', function (Blueprint $table) {
            $table->id();
            $table->string('status_peraturan'); // Contoh: 'berlaku' atau 'tidak_berlaku'
            $table->string('judul_peraturan'); // Nama / Judul regulasi
            $table->string('nomor_peraturan'); // Contoh: No. 12 Tahun 2026
            $table->string('file_path'); // Path file dokumen PDF
            $table->text('tentang')->nullable(); // Ringkasan singkat isi peraturan
            $table->unsignedBigInteger('jumlah_diunduh')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jdihs');
    }
};
