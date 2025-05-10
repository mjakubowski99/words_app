<?php

namespace Tests\Integration\Flashcards\Application\Command;

use App\Models\LearningSessionFlashcard;
use Flashcard\Application\Command\UpdateRatingsHandler;
use Flashcard\Domain\Models\Rating;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Exercise\IExerciseScore;
use Shared\Utils\ValueObjects\ExerciseEntryId;
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
            $this->createSessionFlashcard(['rating' => null, 'exercise_entry_id' => 1]),
            $this->createSessionFlashcard(['rating' => null, 'exercise_entry_id' => 2])
        ];
        $ratings = [
            \Mockery::mock(IExerciseScore::class)->allows([
                'getExerciseEntryId' => new ExerciseEntryId($flashcards[0]->exercise_entry_id),
                'getScore' => 0.90,
            ]),
            \Mockery::mock(IExerciseScore::class)->allows([
                'getExerciseEntryId' => new ExerciseEntryId($flashcards[1]->exercise_entry_id),
                'getScore' => 0.2,
            ]),
        ];

        // WHEN
        $this->handler->handle($ratings);

        // THEN
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $flashcards[0]->id,
            'rating' => Rating::VERY_GOOD,
        ]);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $flashcards[1]->id,
            'rating' => Rating::UNKNOWN,
        ]);
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
