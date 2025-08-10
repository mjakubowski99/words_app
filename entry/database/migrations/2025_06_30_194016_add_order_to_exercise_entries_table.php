<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exercise_entries', function (Blueprint $table) {
            $table->unsignedSmallInteger('order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('exercise_entries', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
