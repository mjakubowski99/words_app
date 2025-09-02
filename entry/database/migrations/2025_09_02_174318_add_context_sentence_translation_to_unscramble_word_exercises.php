<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('unscramble_word_exercises', function (Blueprint $table) {
            $table->string('context_sentence_translation')->after('context_sentence')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('unscramble_word_exercises', function (Blueprint $table) {
            $table->dropColumn('context_sentence_translation');
        });
    }
};
