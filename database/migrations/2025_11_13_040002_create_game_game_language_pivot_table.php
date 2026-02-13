<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_game_language', function (Blueprint $table) {
            $table->uuid('game_id');
            $table->uuid('game_language_id');
            $table->primary(['game_id', 'game_language_id']);
            
            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->onDelete('cascade');
            
            $table->foreign('game_language_id')
                ->references('id')
                ->on('game_languages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_game_language');
    }
};
