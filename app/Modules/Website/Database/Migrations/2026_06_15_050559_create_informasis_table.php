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
        Schema::create('informasis', function (Blueprint $table) {
            $table->id();
            $table->json('kejuruan')->nullable();
            $table->json('gedung_fasilitas')->nullable();
            $table->json('kelas_workshop')->nullable();
            $table->json('alumni')->nullable();
            $table->json('testimoni')->nullable();
            $table->json('kerjasama')->nullable();
            $table->json('faq')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasis');
    }
};
