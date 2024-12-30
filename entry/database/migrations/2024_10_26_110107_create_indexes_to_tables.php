<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('flashcard_categories', function (Blueprint $table) {
            $table->index('user_id');
        });
        Schema::table('flashcards', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('flashcard_category_id');
        });
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->index('learning_session_id');
            $table->index('flashcard_id');
        });
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->index('flashcard_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('flashcard_categories', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['flashcard_category_id']);
        });
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->dropIndex(['learning_session_id']);
            $table->dropIndex(['flashcard_id']);
        });
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->dropIndex(['flashcard_category_id']);
        });
    }
};
