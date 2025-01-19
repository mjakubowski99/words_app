<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository\BulkDelete;

use Tests\Base\FlashcardTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;

class FlashcardRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private FlashcardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardRepository::class);
    }

    public function test__bulkDelete_ShouldDeleteOnlyUserFlashcards(): void
    {
        // GIVEN
        $user = $this->createUser();
        $user_flashcard = $this->createUserFlashcard($user);
        $other_flashcard = $this->createFlashcard();

        // WHEN
        $this->repository->bulkDelete($user->getId(), [
            $user_flashcard->getId(),
            $other_flashcard->getId(),
        ]);

        // THEN
        $this->assertDatabaseHas('flashcards', [
            'id' => $other_flashcard->getId(),
        ]);
        $this->assertDatabaseMissing('flashcards', [
            'id' => $user_flashcard->getId(),
        ]);
    }

    public function test__bulkDelete_ShouldDeleteFlashcardWithAllData(): void
    {
        // GIVEN
        $user = $this->createUser();
        $user_flashcard = $this->createUserFlashcard($user);
        $sm_two_flashcard = $this->createSmTwoFlashcard(['flashcard_id' => $user_flashcard->id]);
        $learning_session_flashcard = $this->createLearningSessionFlashcard(['flashcard_id' => $user_flashcard->id]);

        // WHEN
        $this->repository->bulkDelete($user->getId(), [
            $user_flashcard->getId(),
        ]);

        // THEN
        $this->assertDatabaseMissing('flashcards', [
            'id' => $user_flashcard->id,
        ]);
        $this->assertDatabaseMissing('sm_two_flashcards', [
            'flashcard_id' => $sm_two_flashcard->flashcard_id,
        ]);
        $this->assertDatabaseMissing('learning_session_flashcards', [
            'id' => $learning_session_flashcard->id,
        ]);
    }
}
