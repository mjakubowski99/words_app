<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository\GetLatestSessionFlashcardIds;

use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\ValueObjects\FlashcardId;
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

    public function test__getLatestSessionFlashcardIds_ShouldReturnLimitedLatestSessionFlashcardIds(): void
    {
        // GIVEN
        $session = $this->createLearningSession();
        $session_flashcards = [
            $this->createLearningSessionFlashcard([
                'learning_session_id' => $session->id,
                'created_at' => now()->subMinute(),
            ]),
            $this->createLearningSessionFlashcard([
                'learning_session_id' => $session->id,
                'created_at' => now()->subSecond(),
            ]),
            $this->createLearningSessionFlashcard([
                'learning_session_id' => $session->id,
                'created_at' => now()->subSeconds(2),
            ]),
        ];

        // WHEN
        $results = $this->repository->getLatestSessionFlashcardIds($session->getId(), 2);

        // THEN
        $this->assertCount(2, $results);
        $this->assertInstanceOf(FlashcardId::class, $results[0]);
        $this->assertInstanceOf(FlashcardId::class, $results[1]);
        $this->assertSame($session_flashcards[1]->flashcard->id, $results[0]->getValue());
        $this->assertSame($session_flashcards[2]->flashcard->id, $results[1]->getValue());
    }
}
