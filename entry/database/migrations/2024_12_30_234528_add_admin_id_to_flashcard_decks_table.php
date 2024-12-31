<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('flashcard_decks', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->change();
            $table->uuid('admin_id')->nullable();
            $table->foreign('admin_id')
                ->references('id')
                ->on('admins');
        });

        DB::statement('
            ALTER TABLE flashcard_decks ADD CONSTRAINT check_flashcard_decks_user_id_admin_id_both_not_null
            CHECK (
                (admin_id IS NOT NULL AND user_id IS NULL)
                OR
                (admin_id IS NULL AND user_id IS NOT NULL)
            )
        ');
    }

    public function down(): void
    {
        Schema::table('flashcard_decks', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        DB::statement('
            ALTER TABLE flashcard_decks DROP CONSTRAINT check_flashcard_decks_user_id_admin_id_both_not_null
        ');
    }
};
