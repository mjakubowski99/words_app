<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository\Delete;

use Tests\Base\FlashcardTestCase;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;

class FlashcardRepositoryTest extends FlashcardTestCase
{
    private FlashcardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardRepository::class);
    }

    public function test__delete_ShouldDeleteFlashcard(): void
    {
        // GIVEN
        $flashcard = $this->createFlashcard();
        $this->createSmTwoFlashcard(['flashcard_id' => $flashcard->id]);
        $this->createLearningSessionFlashcard(['flashcard_id' => $flashcard->id]);

        // WHEN
        $this->repository->delete($flashcard->getId());

        // THEN
        $this->assertDatabaseMissing('flashcards', [
            'id' => $flashcard->id,
        ]);
        $this->assertDatabaseMissing('sm_two_flashcards', [
            'flashcard_id' => $flashcard->id,
        ]);
        $this->assertDatabaseMissing('learning_session_flashcards', [
            'flashcard_id' => $flashcard->id,
        ]);
    }
}
