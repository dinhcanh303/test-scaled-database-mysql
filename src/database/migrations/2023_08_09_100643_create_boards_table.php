<?php

use Database\Create\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Database::createTable('boards',function (Blueprint $table) {
        $table->unsignedBigInteger('board_id');
        $table->unsignedBigInteger('post_id');
        $table->timestamps();
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
