<?php

namespace Database\Seeders;

use App\Models\FlashcardCategory;
use Illuminate\Database\Seeder;

class MainCategorySeeder extends Seeder
{
    public function run(): void
    {
        if (FlashcardCategory::query()->where('tag', \Flashcard\Domain\Models\FlashcardCategory::MAIN)->exists()) {
            return;
        }

        FlashcardCategory::factory()->create([
            'tag' => \Flashcard\Domain\Models\FlashcardCategory::MAIN,
            'name' => 'Main category',
            'user_id' => null,
        ]);
    }
}
