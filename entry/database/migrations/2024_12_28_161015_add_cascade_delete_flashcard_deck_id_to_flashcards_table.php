<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropForeign(['flashcard_category_id']);
            $table->foreign('flashcard_deck_id')
                ->references('id')
                ->on('flashcard_decks')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropForeign(['flashcard_deck_id']);
            $table->foreign('flashcard_deck_id')
                ->references('id')
                ->on('flashcard_decks');
        });
    }
};
