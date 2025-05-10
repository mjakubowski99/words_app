<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->unsignedTinyInteger('exercise_type')
                ->nullable()
                ->default(null);
        });
    }

    public function down(): void
    {
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->dropColumn('exercise_type');
        });
    }
};
