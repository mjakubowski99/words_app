<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exercise_entries', function (Blueprint $table) {
            $table->dropColumn('session_flashcard_id');
        });
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->unsignedBigInteger('exercise_entry_id')->nullable();
            $table->index('exercise_entry_id');
        });
    }

    public function down(): void
    {
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->dropColumn('exercise_entry_id');
        });
        Schema::table('exercise_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('session_flashcard_id')->nullable();
            $table->index('session_flashcard_id');
        });
    }
};
