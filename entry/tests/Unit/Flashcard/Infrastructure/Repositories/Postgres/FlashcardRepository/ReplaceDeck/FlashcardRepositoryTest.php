<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository\ReplaceDeck;

use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Base\FlashcardTestCase;

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
    public function replaceDeck_replaceDeckForCorrectFlashcards(): void
    {
        // GIVEN
        $actual_deck = $this->createFlashcardDeck();
        $flashcards = [
            $this->createFlashcard(['flashcard_deck_id' => $actual_deck->id]),
            $this->createFlashcard(['flashcard_deck_id' => $actual_deck->id]),
        ];
        $other_flashcard = $this->createFlashcard();
        $new_deck = $this->createFlashcardDeck();

        // WHEN
        $this->repository->replaceDeck($actual_deck->getId(), $new_deck->getId());

        // THEN
        foreach ($flashcards as $flashcard) {
            $this->assertDatabaseHas('flashcards', [
                'id' => $flashcard->id,
                'flashcard_deck_id' => $new_deck->id,
            ]);
        }
        $this->assertDatabaseHas('flashcards', [
            'id' => $other_flashcard->id,
            'flashcard_deck_id' => $other_flashcard->flashcard_deck_id,
        ]);
    }
}
