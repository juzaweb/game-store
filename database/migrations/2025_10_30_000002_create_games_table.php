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
        Schema::create(
            'games',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code', 16)->unique();
                $table->bigInteger('views')->index()->default(0);
                $table->string('status', 10)->index()->default('published');

                $table->foreignUuid('category_id')
                    ->nullable()
                    ->constrained('game_categories')
                    ->onDelete('cascade');
                $table->datetimes();
            }
        );

        Schema::create(
            'game_translations',
            function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug', 150)->index();
                $table->string('locale', 5)->index();
                $table->text('content')->nullable();
                $table->foreignUuid('game_id')
                    ->constrained('games')
                    ->onDelete('cascade');

                $table->unique(['game_id', 'locale']);
                $table->unique(['slug']);
                $table->datetimes();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_translations');
        Schema::dropIfExists('games');
    }
};
