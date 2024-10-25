<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\NextSessionFlashcardsRepository;

use App\Models\LearningSessionFlashcard;
use Tests\TestCase;
use App\Models\Flashcard;
use App\Models\LearningSession;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\NextSessionFlashcardsRepository;

class NextSessionFlashcardsRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private NextSessionFlashcardsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(NextSessionFlashcardsRepository::class);
    }

    public function test__find_ShouldFindCorrectObject(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        $flashcards = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getSessionId()->getValue());
    }

    public function test__save_ShouldSaveObject(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        $flashcard = Flashcard::factory()->create();
        $object = new NextSessionFlashcards(
            $session->getId(),
            $session->user->toOwner(),
            $session->category->toDomainModel(),
            8,
            2,
            10
        );
        $object->addNext($flashcard->toDomainModel());

        // WHEN
        $this->repository->save($object);

        // THEN
        $this->assertDatabaseHas('learning_session_flashcards', [
            'learning_session_id' => $session->id,
            'flashcard_id' => $flashcard->id,
            'rating' => null,
        ]);
    }
}
