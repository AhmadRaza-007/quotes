<?php
// Create a new migration to add category_id to api_keys table
// database/migrations/2025_10_29_000002_add_category_id_to_api_keys_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToApiKeysTable extends Migration
{
    public function up()
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('api_key_categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}
