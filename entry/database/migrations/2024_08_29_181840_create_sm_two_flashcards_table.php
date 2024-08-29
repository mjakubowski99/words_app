<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sm_two_flashcards', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->unsignedBigInteger('flashcard_id');
            $table->unsignedFloat('repetition_ratio');
            $table->unsignedSmallInteger('repetition_interval');
            $table->timestamps();

            $table->primary(['user_id', 'flashcard_id']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('flashcard_id')
                ->references('id')
                ->on('flashcards');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sm_two_flashcards');
    }
};
