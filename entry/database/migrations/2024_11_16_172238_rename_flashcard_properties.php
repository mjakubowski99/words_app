<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('flashcards', function (Blueprint $table) {
            $table->renameColumn('word', 'front_word');
            $table->renameColumn('word_lang', 'front_lang');
            $table->renameColumn('translation', 'back_word');
            $table->renameColumn('translation_lang', 'back_lang');
            $table->renameColumn('context', 'front_context');
            $table->renameColumn('context_translation', 'back_context');
        });
    }

    public function down(): void
    {
        Schema::table('flashcards', function (Blueprint $table) {
            $table->renameColumn('front_word', 'word');
            $table->renameColumn('front_lang', 'word_lang');
            $table->renameColumn('back_word', 'translation');
            $table->renameColumn('back_lang', 'translation_lang');
            $table->renameColumn('front_context', 'context');
            $table->renameColumn('back_context', 'context_translation');
        });
    }
};
