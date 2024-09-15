<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\SessionReadRepository;

use App\Models\LearningSession;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\Rating;
use Flashcard\Infrastructure\Repositories\SessionReadRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Enum\SessionStatus;
use Tests\TestCase;

class SessionReadRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private SessionReadRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(SessionReadRepository::class);
    }

    public function test__find_ShouldFindCorrectObject(): void
    {
        // GIVEN
        $other_session = LearningSession::factory()->create();
        $session = LearningSession::factory()->create([
            'status' => SessionStatus::FINISHED,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getId()->getValue());
        $this->assertSame($session->cards_per_session, $result->getCardsPerSession());
        $this->assertSame(0, $result->getProgress());
        $this->assertTrue($result->isFinished());
    }

    public function test__find_WhenSessionHasRatedFlashcards_ShouldReturnCorrectProgress(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create([
            'cards_per_session' => 5,
        ]);
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => Rating::GOOD,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getId()->getValue());
        $this->assertSame(1, $result->getProgress());
    }

    public function test__find_WhenSessionHasNotRatedFlashcards_ShouldReturnCorrectProgress(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create([
            'cards_per_session' => 5,
        ]);
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getId()->getValue());
        $this->assertSame(0, $result->getProgress());
    }
}
