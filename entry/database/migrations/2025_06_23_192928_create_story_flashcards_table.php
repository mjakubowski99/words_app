<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('story_flashcards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('story_id');
            $table->unsignedBigInteger('flashcard_id');
            $table->string('sentence_override')->nullable();
            $table->timestamps();

            $table->foreign('story_id')
                ->references('id')
                ->on('stories')
                ->onDelete('cascade');
            $table->foreign('flashcard_id')
                ->references('id')
                ->on('flashcards')
                ->onDelete('cascade');

            $table->index('story_id');
            $table->index('flashcard_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_flashcards');
    }
};
