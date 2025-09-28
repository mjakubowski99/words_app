<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_language', 3)->default('pl');
            $table->string('learning_language', 3)->default('en');
            $table->boolean('profile_completed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_language');
            $table->dropColumn('learning_language');
            $table->dropColumn('profile_completed');
        });
    }
};
