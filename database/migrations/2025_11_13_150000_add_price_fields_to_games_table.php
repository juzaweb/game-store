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
        Schema::table(
            'games',
            function (Blueprint $table) {
                $table->decimal('price', 20, 2)->default(0)->nullable();
                $table->decimal('compare_price', 20, 2)->nullable();
                $table->boolean('is_free')->index()->default(true);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'games',
            function (Blueprint $table) {
                $table->dropColumn(['price', 'compare_price', 'is_free']);
            }
        );
    }
};
