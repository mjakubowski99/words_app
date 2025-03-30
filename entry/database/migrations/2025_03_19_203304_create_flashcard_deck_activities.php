<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('flashcard_deck_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flashcard_deck_id');
            $table->uuid('user_id');
            $table->timestamp('last_viewed_at')->nullable();

            $table->foreign('flashcard_deck_id')
                ->references('id')
                ->on('flashcard_decks')
                ->onDelete('CASCADE');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE');

            $table->index('user_id');
            $table->index('flashcard_deck_id');

            $table->unique(['user_id', 'flashcard_deck_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcard_deck_activities');
    }
};
