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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('title');
            $table->text('youtube_url')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->boolean('is_best')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_least_favorite')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['album_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
