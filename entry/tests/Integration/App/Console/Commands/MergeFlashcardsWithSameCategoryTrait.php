<?php

declare(strict_types=1);

namespace Tests\Integration\App\Console\Commands;

use App\Models\Flashcard;
use App\Models\FlashcardCategory;
use Illuminate\Support\Collection;

trait MergeFlashcardsWithSameCategoryTrait
{
    private function createFlashcard(array $attributes = []): Flashcard
    {
        return Flashcard::factory()->create($attributes);
    }

    private function createFlashcardCategory(array $attributes = []): FlashcardCategory
    {
        return FlashcardCategory::factory()->create($attributes);
    }

    private function generateFlashcardsForCategories(array|Collection $categories): array|Collection
    {
        $flashcards = [];
        foreach ($categories as $category) {
            $flashcards[] = Flashcard::factory()->create(['flashcard_category_id' => $category->id]);
        }

        return $flashcards;
    }

    private function assertFlashcardsHasNewCategory(array|Collection $flashcards, FlashcardCategory $expected_category): void
    {
        foreach ($flashcards as $flashcard) {
            $this->assertDatabaseHas('flashcards', [
                'id' => $flashcard->id,
                'flashcard_category_id' => $expected_category->id,
            ]);
        }
    }

    private function assertFlashcardsHasSameCategory(array|Collection $flashcards): void
    {
        foreach ($flashcards as $flashcard) {
            $this->assertDatabaseHas('flashcards', [
                'id' => $flashcard->id,
                'flashcard_category_id' => $flashcard->flashcard_category_id,
            ]);
        }
    }

    private function assertFlashcardsCategoriesDeleted(array|Collection $categories): void
    {
        foreach ($categories as $category) {
            $this->assertDatabaseMissing('flashcard_categories', [
                'id' => $category->id,
            ]);
        }
    }
}
