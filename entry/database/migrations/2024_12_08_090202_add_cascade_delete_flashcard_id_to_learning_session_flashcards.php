<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->dropForeign(['flashcard_id']);
            $table->foreign('flashcard_id')
                ->references('id')
                ->on('flashcards')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->dropForeign(['flashcard_id']);
            $table->foreign('flashcard_id')
                ->references('id')
                ->on('flashcards');
        });
    }
};
