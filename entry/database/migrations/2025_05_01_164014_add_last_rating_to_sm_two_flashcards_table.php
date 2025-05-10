<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sm_two_flashcards', function (Blueprint $table) {
            $table->smallInteger('last_rating')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sm_two_flashcards', function (Blueprint $table) {
            $table->dropColumn('last_rating');
        });
    }
};
