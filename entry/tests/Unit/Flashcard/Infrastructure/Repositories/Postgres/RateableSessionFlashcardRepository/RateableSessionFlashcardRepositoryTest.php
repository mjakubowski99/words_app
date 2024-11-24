<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\RateableSessionFlashcardRepository;

use Tests\TestCase;
use App\Models\User;
use Shared\Enum\SessionStatus;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\RateableSessionFlashcardsRepository;

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
            'rating' => null,
        ]);
        $flashcard = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getSessionId()->getValue());
        $this->assertSame($flashcard->id, $result->getRateableSessionFlashcards()[0]->getId()->getValue());
        $this->assertFalse($result->getRateableSessionFlashcards()[0]->rated());
    }

    public function test__find_ShouldFindOnlyUnratedFlashcards(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => Rating::GOOD,
        ]);
        $flashcard = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getSessionId()->getValue());
        $this->assertSame($session->user_id, $result->getOwner()->getId()->getValue());
        $this->assertSame(1, count($result->getRateableSessionFlashcards()));
        $this->assertSame($flashcard->id, $result->getRateableSessionFlashcards()[0]->getId()->getValue());
    }

    public function test__save_ShouldPersistFlashcardRatings(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        $not_to_rate = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);
        $learning_session_flashcards = LearningSessionFlashcard::factory(2)->create([
            'learning_session_id' => $session->id,
            'rating' => null,
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
                    $learning_session_flashcards[0]->getId(),
                    $learning_session_flashcards[0]->flashcard->getId(),
                ),
                new RateableSessionFlashcard(
                    $learning_session_flashcards[1]->getId(),
                    $learning_session_flashcards[1]->flashcard->getId(),
                ),
            ]
        );
        $flashcards->rate($learning_session_flashcards[0]->getId(), Rating::WEAK);
        $flashcards->rate($learning_session_flashcards[1]->getId(), Rating::GOOD);

        // WHEN
        $this->repository->save($flashcards);

        // THEN
        $this->assertDatabaseHas('learning_sessions', [
            'id' => $session->id,
            'status' => SessionStatus::FINISHED->value,
        ]);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $learning_session_flashcards[0]->id,
            'rating' => Rating::WEAK,
        ]);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $learning_session_flashcards[1]->id,
            'rating' => Rating::GOOD,
        ]);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $not_to_rate->id,
            'rating' => null,
        ]);
    }
}
