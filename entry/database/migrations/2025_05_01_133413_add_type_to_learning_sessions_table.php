<?php

declare(strict_types=1);

use Shared\Enum\SessionType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->string('type')->default(SessionType::FLASHCARD->value);
        });
    }

    public function down(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
