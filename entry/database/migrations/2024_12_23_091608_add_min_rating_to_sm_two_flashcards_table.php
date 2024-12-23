<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sm_two_flashcards', function (Blueprint $table) {
            $table->integer('min_rating')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('sm_two_flashcards', function (Blueprint $table) {
            $table->dropColumn('min_rating');
        });
    }
};
