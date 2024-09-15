<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\RateableSessionFlashcardRepository;

use App\Models\LearningSession;
use App\Models\LearningSessionFlashcard;
use App\Models\User;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Domain\Models\Rating;
use Flashcard\Infrastructure\Repositories\RateableSessionFlashcardsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Enum\SessionStatus;
use Tests\TestCase;

class RateableSessionFlashcardRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private RateableSessionFlashcardsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(RateableSessionFlashcardsRepository::class);
    }

    public function test__find_ShouldFindCorrectObject(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getSessionId()->getValue());
        $this->assertSame($session->status, $result->getStatus()->value);
        $this->assertSame($session->user_id, $result->getOwner()->getId()->getValue());
    }

    public function test__create_ShouldPersistFlashcardRatings(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        $learning_session_flashcard = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
        ]);
        $user = User::factory()->create();

        $flashcards = new RateableSessionFlashcards(
            $session->getId(),
            $user->toOwner(),
            SessionStatus::IN_PROGRESS,
            9,
            10,
            [
                new RateableSessionFlashcard(
                    $learning_session_flashcard->getId(),
                    $learning_session_flashcard->flashcard->getId(),
                )
            ]
        );
        $flashcards->rate($learning_session_flashcard->getId(), Rating::WEAK);

        // WHEN
        $this->repository->save($flashcards);

        // THEN
        $this->assertDatabaseHas('learning_sessions', [
            'id' => $session->id,
            'status' => SessionStatus::FINISHED->value,
        ]);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'learning_session_id' => $session->id,
            'rating' => Rating::WEAK,
        ]);
    }
}
