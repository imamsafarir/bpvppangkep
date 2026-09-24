<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (! Schema::hasColumn('comments', 'content_id')) {
                $table->foreignId('content_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('contents')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'content_id')) {
                $table->dropForeign(['content_id']);
                $table->dropColumn('content_id');
            }
        });
    }
};
