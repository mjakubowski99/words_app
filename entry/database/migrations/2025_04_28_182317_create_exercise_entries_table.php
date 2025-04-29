<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('session_flashcard_id')->nullable();
            $table->string('correct_answer');
            $table->string('last_answer')->nullable();
            $table->boolean('last_answer_correct')->nullable()->default(null);
            $table->float('score')->default(0.0);
            $table->unsignedSmallInteger('answers_count')->default(0);
            $table->timestamps();

            $table->foreign('exercise_id')
                ->references('id')
                ->on('exercises')
                ->onDelete('cascade');

            $table->index('exercise_id');

            $table->foreign('session_flashcard_id')
                ->references('id')
                ->on('learning_session_flashcards')
                ->onDelete('cascade');

            $table->index('session_flashcard_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_entries');
    }
};
