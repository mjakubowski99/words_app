<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('flashcard_poll_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->unsignedBigInteger('flashcard_id');
            $table->unsignedSmallInteger('easy_ratings_count')->default(0);
            $table->unsignedSmallInteger('easy_ratings_count_to_purge')->default(0);
            $table->unsignedSmallInteger('leitner_level');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE');

            $table->foreign('flashcard_id')
                ->references('id')
                ->on('flashcards')
                ->onDelete('CASCADE');

            $table->index('user_id');
            $table->index('flashcard_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcard_poll_items');
    }
};
