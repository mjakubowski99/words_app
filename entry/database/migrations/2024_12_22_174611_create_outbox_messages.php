<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbox_messages', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('type');
            $table->json('payload');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->json('errors')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('retry_after')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_messages');
    }
};
