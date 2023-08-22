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
        Database::createTable('posts',function (BlueprintExtended $table) {
            $table->id();
            $table->json('data')->nullable();
            $table->appendCommonFields();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Database::dropTable('posts');
    }
};
