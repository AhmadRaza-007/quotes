<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('profile_post_id')->constrained('profile_posts')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'profile_post_id']);
            $table->index(['profile_post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_likes');
    }
};
