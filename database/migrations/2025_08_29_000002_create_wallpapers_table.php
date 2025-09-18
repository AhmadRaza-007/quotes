<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallpapers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('title', 255);
            $table->string('file_path', 255);
            $table->string('file_url', 255); // required
            $table->string('media_type', 255)->default('image'); // image | video | live
            $table->text('thumbnail_url')->nullable(); // text instead of string
            $table->string('mime_type', 255)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallpapers');
    }
};
