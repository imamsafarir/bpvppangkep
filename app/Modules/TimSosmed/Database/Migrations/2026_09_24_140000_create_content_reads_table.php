<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_reads')) {
            Schema::create('content_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('last_read_at');
                $table->timestamps();

                $table->unique(['content_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('content_reads');
    }
};
