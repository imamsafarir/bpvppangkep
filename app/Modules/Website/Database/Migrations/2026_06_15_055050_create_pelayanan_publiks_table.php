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
        Schema::create('pelayanan_publiks', function (Blueprint $table) {
            $table->id();
            $table->text('maklumat_pelayanan')->nullable();
            $table->text('standar_pelayanan')->nullable();

            // Alur pelayanan biasanya berupa gambar bagan/infografis
            $table->string('foto_alur_pelayanan')->nullable();
            $table->text('deskripsi_alur_pelayanan')->nullable();

            // Kolom untuk menaruh Link Survey atau Konten Penjelasan
            $table->text('survey_kepuasan_masyarakat')->nullable();
            $table->text('survey_kebutuhan_pelatihan')->nullable();
            $table->text('survey_kebekerjaan')->nullable();

            // Hasil Indeks Kepuasan Masyarakat (IKM)
            $table->text('indeks_kepuasan_masyarakat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelayanan_publiks');
    }
};
