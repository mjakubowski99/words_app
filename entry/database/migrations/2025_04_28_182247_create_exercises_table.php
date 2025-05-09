<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('exercise_type');
            $table->string('status');
            $table->uuid('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
