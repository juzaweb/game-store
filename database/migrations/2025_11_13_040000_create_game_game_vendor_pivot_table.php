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
        Schema::create('game_game_vendor', function (Blueprint $table) {
            $table->uuid('game_id');
            $table->uuid('game_vendor_id');
            $table->primary(['game_id', 'game_vendor_id']);
            
            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->onDelete('cascade');
            
            $table->foreign('game_vendor_id')
                ->references('id')
                ->on('game_vendors')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_game_vendor');
    }
};
