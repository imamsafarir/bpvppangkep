<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            // Relationship ID
            $table->foreignId('planner_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('instruktur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('users')->nullOnDelete();

            // Informasi Kegiatan
            $table->string('jenis_konten')->nullable();
            $table->string('nama_kegiatan');
            $table->date('tanggal_kegiatan');
            $table->text('brief')->nullable();
            $table->text('caption')->nullable();

            // Kolom Link (Sudah digabung)
            $table->string('link_referensi')->nullable();
            $table->text('link_media_mentah')->nullable();
            $table->text('link_hasil_edit')->nullable();
            $table->string('link_postingan')->nullable();

            $table->string('status')->default('Draft');

            $table->date('tanggal_posting')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
