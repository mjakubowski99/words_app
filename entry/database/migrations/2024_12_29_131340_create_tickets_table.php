<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('type');
            $table->string('description');
            $table->string('reportable_id')->nullable();
            $table->string('reportable_type')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->index(['reportable_id', 'reportable_type']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
