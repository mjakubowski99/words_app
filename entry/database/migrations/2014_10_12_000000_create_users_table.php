<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('provider_id')->nullable();
            $table->string('provider_type')->nullable();
            $table->string('picture')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['provider_id', 'provider_type']);
            $table->unique(['provider_id', 'provider_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
