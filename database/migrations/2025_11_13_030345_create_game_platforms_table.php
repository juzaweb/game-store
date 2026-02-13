<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'game_platforms',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug', 150)->index();
                $table->datetimes();

                $table->unique(['slug']);
            }
        );

        Schema::create('game_game_platform', function (Blueprint $table) {
            $table->uuid('game_id');
            $table->uuid('game_platform_id');
            $table->primary(['game_id', 'game_platform_id']);

            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->onDelete('cascade');

            $table->foreign('game_platform_id')
                ->references('id')
                ->on('game_platforms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_game_platform');
        Schema::dropIfExists('game_platforms');
    }
};
