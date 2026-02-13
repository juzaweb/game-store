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
            'game_vendors',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->datetimes();
            }
        );

        Schema::create(
            'game_vendor_translations',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug', 150)->index();
                $table->string('locale', 5)->index();
                $table->foreignUuid('game_vendor_id')
                    ->constrained('game_vendors')
                    ->onDelete('cascade');

                $table->unique(['game_vendor_id', 'locale']);
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
        Schema::dropIfExists('game_vendor_translations');
        Schema::dropIfExists('game_vendors');
    }
};
