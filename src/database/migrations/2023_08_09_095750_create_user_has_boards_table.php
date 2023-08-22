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
        $schema = DB::connection()->getSchemaBuilder();
        $schema->blueprintResolver(function ($table, $callback) {
            return new BlueprintExtended($table, $callback);
        });
        $schema->create('user_has_boards', function (BlueprintExtended $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('board_id');
            $table->appendCommonFields();
            $table->index(['user_id','board_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_has_boards');
    }
};
