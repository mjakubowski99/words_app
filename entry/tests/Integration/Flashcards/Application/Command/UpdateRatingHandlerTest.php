<?php

declare(strict_types=1);
use Flashcard\Domain\Models\Rating;
use Shared\Exercise\IExerciseScore;
use App\Models\LearningSessionFlashcard;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Flashcard\Application\Command\UpdateRatingsHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->handler = app(UpdateRatingsHandler::class);
});
test('handle should update rating and update repetition algorithm', function () {
    // GIVEN
    $flashcards = [
        createSessionFlashcard(['rating' => null, 'exercise_entry_id' => 1]),
        createSessionFlashcard(['rating' => null, 'exercise_entry_id' => 2]),
    ];
    $ratings = [
        Mockery::mock(IExerciseScore::class)->allows([
            'getExerciseEntryId' => new ExerciseEntryId($flashcards[0]->exercise_entry_id),
            'getScore' => 0.90,
        ]),
        Mockery::mock(IExerciseScore::class)->allows([
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
});
function createSessionFlashcard(array $attributes = []): LearningSessionFlashcard
{
    return LearningSessionFlashcard::factory()->create($attributes);
}
