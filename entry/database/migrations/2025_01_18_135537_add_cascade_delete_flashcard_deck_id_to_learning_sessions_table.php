<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->dropForeign(['flashcard_category_id']);
            $table->foreign('flashcard_deck_id')
                ->references('id')
                ->on('flashcard_decks')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->dropForeign(['flashcard_deck_id']);
            $table->foreign('flashcard_deck_id')
                ->references('id')
                ->on('flashcard_decks');
        });
    }
};
