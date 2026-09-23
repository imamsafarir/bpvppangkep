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
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            // Gunakan 'name' agar standar dengan model Laravel lainnya
            $table->string('name');
            // Slug penting untuk pencarian data & keamanan URL (contoh: facebook, instagram)
            $table->string('slug')->unique();
            // Kolom icon untuk menyimpan nama icon Heroicons atau Lucide di Filament
            $table->string('icon')->nullable();
            // Tambahkan status aktif/nonaktif jika nanti ingin mematikan platform tertentu
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
