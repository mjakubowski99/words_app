<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::rename('flashcard_categories', 'flashcard_decks');

        Schema::table('flashcards', function (Blueprint $table) {
            $table->renameColumn('flashcard_category_id', 'flashcard_deck_id');
        });

        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->renameColumn('flashcard_category_id', 'flashcard_deck_id');
        });
    }

    public function down(): void
    {
        Schema::rename('flashcard_decks', 'flashcard_categories');

        Schema::table('flashcards', function (Blueprint $table) {
            $table->renameColumn('flashcard_deck_id', 'flashcard_category_id');
        });

        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->renameColumn('flashcard_deck_id', 'flashcard_category_id');
        });
    }
};
