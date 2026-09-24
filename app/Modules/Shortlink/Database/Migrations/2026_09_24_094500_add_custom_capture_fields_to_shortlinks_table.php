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
        Schema::table('shortlinks', function (Blueprint $table) {
            $table->string('custom_title')->nullable()->after('capture_fields');
            $table->text('custom_description')->nullable()->after('custom_title');
            $table->string('custom_button_text')->nullable()->after('custom_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shortlinks', function (Blueprint $table) {
            $table->dropColumn(['custom_title', 'custom_description', 'custom_button_text']);
        });
    }
};
