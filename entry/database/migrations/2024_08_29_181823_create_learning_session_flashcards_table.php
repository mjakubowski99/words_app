<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('learning_session_flashcards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learning_session_id');
            $table->unsignedBigInteger('flashcard_id');
            $table->integer('rating')->nullable();
            $table->timestamps();

            $table->foreign('learning_session_id')
                ->references('id')
                ->on('learning_sessions')
                ->onDelete('cascade');

            $table->foreign('flashcard_id')
                ->references('id')
                ->on('flashcards');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_flashcards');
    }
};
