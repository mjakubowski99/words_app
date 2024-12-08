<?php

declare(strict_types=1);

use Shared\Enum\LanguageLevel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('flashcards', function (Blueprint $table) {
            $table->string('language_level')->default(LanguageLevel::DEFAULT);
        });
    }

    public function down(): void
    {
        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropColumn('language_level');
        });
    }
};
