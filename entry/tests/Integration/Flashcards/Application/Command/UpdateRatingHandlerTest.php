<?php

namespace Integration\Flashcards\Application\Command;

use App\Models\LearningSessionFlashcard;
use Flashcard\Application\Command\UpdateRatingsHandler;
use Flashcard\Domain\Models\Rating;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Flashcard\ISessionFlashcardRating;
use Tests\TestCase;

class UpdateRatingHandlerTest extends TestCase
{
    use DatabaseTransactions;

    private UpdateRatingsHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = app(UpdateRatingsHandler::class);
    }

    public function test__handle_ShouldUpdateRatingAndUpdateRepetitionAlgorithm(): void
    {
        //GIVEN
        $flashcards = [
            $this->createSessionFlashcard(['rating' => null]),
            $this->createSessionFlashcard(['rating' => null])
        ];
        $ratings = [
            \Mockery::mock(ISessionFlashcardRating::class)->allows([
                'getSessionFlashcardId' => $flashcards[0]->id,
                'getRating' => Rating::VERY_GOOD,
            ]),
            \Mockery::mock(ISessionFlashcardRating::class)->allows([
                'getSessionFlashcardId' => $flashcards[1]->id,
                'getRating' => Rating::UNKNOWN,
            ]),
        ];

        // WHEN
        $this->handler->handle($ratings);

        // THEN
        foreach ($ratings as $rating) {
            $this->assertDatabaseHas('learning_session_flashcards', [
                'id' => $rating->getSessionFlashcardId(),
                'rating' => $rating->getRating(),
            ]);
        }
        foreach ($flashcards as $flashcard) {
            $flashcard->refresh();
            $this->assertDatabaseHas('sm_two_flashcards', [
                'flashcard_id' => $flashcard->flashcard_id,
                'last_rating' => $flashcard->rating,
            ]);
        }
    }

    private function createSessionFlashcard(array $attributes = []): LearningSessionFlashcard
    {
        return LearningSessionFlashcard::factory()->create($attributes);
    }
}
