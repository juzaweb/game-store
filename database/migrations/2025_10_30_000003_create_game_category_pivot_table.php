<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the pivot table for many-to-many relationship
        Schema::create('game_category', function (Blueprint $table) {
            $table->uuid('game_id');
            $table->uuid('game_category_id');
            $table->primary(['game_id', 'game_category_id']);
            
            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->onDelete('cascade');
            
            $table->foreign('game_category_id')
                ->references('id')
                ->on('game_categories')
                ->onDelete('cascade');
        });

        // Migrate existing category relationships to the pivot table
        DB::statement('
            INSERT INTO game_category (game_id, game_category_id)
            SELECT id, category_id
            FROM games
            WHERE category_id IS NOT NULL
        ');

        // Drop the foreign key and category_id column from games table
        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_category');

        // Restore the category_id column to games table
        Schema::table('games', function (Blueprint $table) {
            $table->foreignUuid('category_id')
                ->nullable()
                ->after('status')
                ->constrained('game_categories')
                ->onDelete('cascade');
        });
    }
};
