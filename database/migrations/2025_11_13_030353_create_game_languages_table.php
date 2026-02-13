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
            'game_languages',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('slug', 150)->index();
                $table->unique(['slug']);
                $table->datetimes();
            }
        );

        Schema::create(
            'game_language_translations',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('locale', 5)->index();
                $table->foreignUuid('game_language_id')
                    ->constrained('game_languages')
                    ->onDelete('cascade');

                $table->unique(['game_language_id', 'locale']);
                $table->datetimes();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_language_translations');
        Schema::dropIfExists('game_languages');
    }
};
