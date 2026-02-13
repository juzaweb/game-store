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
            'game_download_links',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title')->nullable();
                $table->text('url');
                $table->string('size')->nullable();
                $table->string('platform')->nullable();
                $table->integer('order')->default(0);

                $table->foreignUuid('game_id')
                    ->constrained('games')
                    ->onDelete('cascade');
                $table->datetimes();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_download_links');
    }
};
