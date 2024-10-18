<?php

declare(strict_types=1);

namespace Tests\Integration\App\Console\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Console\Commands\MergeFlashcardsWithSameCategoryCommand;

class MergeFlashcardsWithSameCategoryCommandTest extends TestCase
{
    use DatabaseTransactions;
    use MergeFlashcardsWithSameCategoryTrait;

    public function test__handle_mergeFlashcardsToCorrectUserCategory(): void
    {
        // GIVEN
        $user = $this->createUser();
        $categories_to_merge = collect([
            $this->createFlashcardCategory(['name' => 'pies kot', 'user_id' => $user->id]),
            $this->createFlashcardCategory(['name' => 'pies kot', 'user_id' => $user->id]),
        ]);
        $categories_not_to_merge = [
            $this->createFlashcardCategory(['name' => 'jabłko jajko kot', 'user_id' => $user->id]),
            $this->createFlashcardCategory(['name' => 'jabłko jajko kura', 'user_id' => $user->id]),
        ];
        $categories_to_merge = $categories_to_merge->merge([
            $this->createFlashcardCategory(['name' => 'Pies Kot', 'user_id' => $user->id]),
            $this->createFlashcardCategory(['name' => 'PieS Kot', 'user_id' => $user->id]),
        ]);

        $flashcards_to_one_category = $this->generateFlashcardsForCategories($categories_to_merge);
        $flashcards_not_to_one_category = $this->generateFlashcardsForCategories($categories_not_to_merge);

        $expected_category = $categories_to_merge[0];

        // WHEN
        $this->artisan(MergeFlashcardsWithSameCategoryCommand::class);

        // THEN
        $this->assertFlashcardsHasNewCategory($flashcards_to_one_category, $expected_category);
        $this->assertFlashcardsHasSameCategory($flashcards_not_to_one_category);
        $this->assertFlashcardsCategoriesDeleted($categories_to_merge->skip(1));
    }

    public function test__handle_mergeFlashcardToSameUser(): void
    {
        // GIVEN
        $user = $this->createUser();
        $categories_to_merge = collect([
            $this->createFlashcardCategory(['name' => 'pies kot', 'user_id' => $user->id]),
            $this->createFlashcardCategory(['name' => 'pies kot', 'user_id' => $user->id]),
        ]);
        $category_not_to_merge = $this->createFlashcardCategory(['name' => 'pies kot']);
        $flashcards_to_one_category = $this->generateFlashcardsForCategories($categories_to_merge);
        $flashcard_not_to_merge = $this->generateFlashcardsForCategories([$category_not_to_merge])[0];
        $expected_category = $categories_to_merge[0];

        // WHEN
        $this->artisan(MergeFlashcardsWithSameCategoryCommand::class);

        // THEN
        $this->assertFlashcardsHasNewCategory($flashcards_to_one_category, $expected_category);
        $this->assertFlashcardsCategoriesDeleted($categories_to_merge->skip(1));
        $this->assertFlashcardsHasSameCategory([$flashcard_not_to_merge]);
    }
}
