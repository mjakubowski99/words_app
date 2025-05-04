<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->dropColumn('progress_tick');
            $table->boolean('is_additional')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('learning_session_flashcards', function (Blueprint $table) {
            $table->dropColumn('is_additional');
            $table->unsignedBigInteger('progress_tick')->default(0);
        });

        DB::statement('
            WITH cte AS (
                SELECT id, ROW_NUMBER() OVER (ORDER BY id) - 1 AS progress_tick
                FROM learning_session_flashcards
            )
            UPDATE learning_session_flashcards
            SET progress_tick = cte.progress_tick
            FROM cte
            WHERE learning_session_flashcards.id = cte.id
        ');
    }
};
