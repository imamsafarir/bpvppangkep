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
        Schema::create('informasi_publiks', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Isinya: 'berkala', 'serta_merta', 'setiap_saat'
            $table->string('nama_dokumen'); // Judul informasi / nama file
            $table->string('file_path')->nullable(); // Lokasi file PDF/Doc yang diunggah
            $table->text('deskripsi')->nullable(); // Catatan atau ringkasan dokumen
            $table->unsignedBigInteger('jumlah_diunduh')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasi_publiks');
    }
};
