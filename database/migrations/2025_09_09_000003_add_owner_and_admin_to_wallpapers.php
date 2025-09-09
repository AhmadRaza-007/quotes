<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallpapers', function (Blueprint $table) {
            if (!Schema::hasColumn('wallpapers', 'owner_user_id')) {
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete()->after('file_path');
            }
            if (!Schema::hasColumn('wallpapers', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('owner_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallpapers', function (Blueprint $table) {
            if (Schema::hasColumn('wallpapers', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
            if (Schema::hasColumn('wallpapers', 'owner_user_id')) {
                $table->dropConstrainedForeignId('owner_user_id');
            }
        });
    }
};
