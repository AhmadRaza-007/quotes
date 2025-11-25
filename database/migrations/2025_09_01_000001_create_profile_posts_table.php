<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profile_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('wallpaper_id')->constrained('wallpapers')->cascadeOnDelete();
            $table->string('caption')->nullable();
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('comments_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'wallpaper_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['wallpaper_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_posts');
    }
};
