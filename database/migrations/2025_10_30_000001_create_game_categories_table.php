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
            'game_categories',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('parent_id')->nullable()->index();
                $table->datetimes();
                
                $table->foreign('parent_id')
                    ->references('id')
                    ->on('game_categories')
                    ->onDelete('cascade');
            }
        );

        Schema::create(
            'game_category_translations',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug', 150)->index();
                $table->string('locale', 5)->index();
                $table->foreignUuid('game_category_id')
                    ->constrained('game_categories')
                    ->onDelete('cascade');

                $table->unique(['game_category_id', 'locale']);
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
        Schema::dropIfExists('game_category_translations');
        Schema::dropIfExists('game_categories');
    }
};
