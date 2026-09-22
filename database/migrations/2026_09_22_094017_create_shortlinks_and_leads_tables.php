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
        Schema::create('shortlinks', function (Blueprint $table) {
            $table->id();
            $table->string('pegawai_name');
            $table->string('code', 10)->unique();
            $table->text('destination_url');
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->boolean('is_capture_active')->default(false);
            $table->json('capture_fields')->nullable(); // e.g. ["nama", "whatsapp", "email"]
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('shortlink_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shortlink_id')->constrained('shortlinks')->cascadeOnDelete();
            $table->string('nama')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shortlink_leads');
        Schema::dropIfExists('shortlink_pad');
        Schema::dropIfExists('shortlinks');
    }
};
