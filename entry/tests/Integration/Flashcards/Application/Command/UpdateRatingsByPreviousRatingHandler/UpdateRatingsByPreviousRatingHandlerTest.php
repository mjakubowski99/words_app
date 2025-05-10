<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command\UpdateRatingsByPreviousRatingHandler;

use Tests\TestCase;
use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Rating;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Command\UpdateRatingsByPreviousRatingHandler;

class UpdateRatingsByPreviousRatingHandlerTest extends TestCase
{
    use DatabaseTransactions;
    use UpdateRatingsByPreviousRatingHandlerTrait;

    private UpdateRatingsByPreviousRatingHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(UpdateRatingsByPreviousRatingHandler::class);
    }

    public function test__updateRatingsByPreviousRatingHandler_ShouldUpdateRatings(): void
    {
        // GIVEN
        $entry_ids = [1];
        $user = $this->createUser();
        $flashcard = $this->createFlashcard(['user_id' => $user->id]);
        $previous_session_flashcard = $this->createLearningSessionFlashcard([
            'flashcard_id' => $flashcard->id,
            'rating' => Rating::VERY_GOOD,
            'updated_at' => now()->subMinutes(10),
        ]);
        $session_flashcard = $this->createLearningSessionFlashcard([
            'flashcard_id' => $flashcard->id,
            'rating' => null,
            'updated_at' => now(),
            'exercise_entry_id' => $entry_ids[0],
        ]);

        // WHEN
        $this->handler->handle($entry_ids);

        // THEN
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $session_flashcard->id,
            'rating' => Rating::VERY_GOOD,
        ]);
    }

    public function test__updateRatingsByPreviousRatingHandler_ShouldFinishSession(): void
    {
        // GIVEN
        $user = $this->createUser();
        $entry_id = 3;
        $flashcard = $this->createFlashcard(['user_id' => $user->id]);
        $session = $this->createLearningSession([
            'status' => SessionStatus::IN_PROGRESS,
            'cards_per_session' => 1,
        ]);
        $session_flashcard = $this->createLearningSessionFlashcard([
            'learning_session_id' => $session->id,
            'flashcard_id' => $flashcard->id,
            'rating' => null,
            'updated_at' => now(),
            'exercise_entry_id' => $entry_id,
        ]);

        // WHEN
        $this->handler->handle([$entry_id]);

        // THEN
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $session_flashcard->id,
            'rating' => Rating::UNKNOWN,
        ]);
        $this->assertDatabaseHas('learning_sessions', [
            'id' => $session_flashcard->learning_session_id,
            'status' => SessionStatus::FINISHED,
        ]);
    }
}
