<?php

use Database\Supports\BlueprintExtended;
use Database\Supports\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Database::createTable('board_has_posts',function (BlueprintExtended $table) {
            $table->unsignedBigInteger('board_id');
            $table->unsignedBigInteger('post_id');
            $table->appendCommonFields();
            $table->index(['board_id','post_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Database::dropTable('board_has_posts');
    }
};
