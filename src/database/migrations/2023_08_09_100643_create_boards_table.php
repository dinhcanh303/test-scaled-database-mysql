<?php

use Database\Supports\BlueprintExtended;
use Database\Supports\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Database::createTable('boards',function (BlueprintExtended $table) {
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
        Database::dropTable('boards');
    }
};
