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
        Schema::table('themes', function (Blueprint $table) {
            $table->string('media_type')->default('image')->after('theme'); // image | video | live
            $table->string('thumbnail')->nullable()->after('media_type'); // optional preview for videos
            $table->string('mime_type')->nullable()->after('thumbnail');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'thumbnail', 'mime_type', 'file_size']);
        });
    }
};
