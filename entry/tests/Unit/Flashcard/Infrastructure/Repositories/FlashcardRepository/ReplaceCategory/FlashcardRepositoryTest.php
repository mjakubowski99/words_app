<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\FlashcardRepository\ReplaceCategory;

use Tests\Base\FlashcardTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\FlashcardRepository;

class FlashcardRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private FlashcardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardRepository::class);
    }

    /**
     * @test
     */
    public function replaceCategory_replaceCategoryForCorrectFlashcards(): void
    {
        // GIVEN
        $actual_category = $this->createFlashcardCategory();
        $flashcards = [
            $this->createFlashcard(['flashcard_category_id' => $actual_category->id]),
            $this->createFlashcard(['flashcard_category_id' => $actual_category->id]),
        ];
        $other_flashcard = $this->createFlashcard();
        $new_category = $this->createFlashcardCategory();

        // WHEN
        $this->repository->replaceCategory($actual_category->getId(), $new_category->getId());

        // THEN
        foreach ($flashcards as $flashcard) {
            $this->assertDatabaseHas('flashcards', [
                'id' => $flashcard->id,
                'flashcard_category_id' => $new_category->id,
            ]);
        }
        $this->assertDatabaseHas('flashcards', [
            'id' => $other_flashcard->id,
            'flashcard_category_id' => $other_flashcard->flashcard_category_id,
        ]);
    }
}
