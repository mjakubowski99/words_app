<?php

declare(strict_types=1);

namespace Tests\Integration\Exercise\Application\Command\SkipUnscrambleWordExerciseHandler;

use Tests\TestCase;
use App\Models\Exercise;
use App\Models\ExerciseEntry;
use Flashcard\Domain\Models\Rating;
use App\Models\UnscrambleWordExercise;
use App\Models\LearningSessionFlashcard;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\ExerciseStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Exercise\Application\Command\SkipExercise\SkipUnscrambleWordExerciseHandler;

class SkipUnscrambleWordExerciseHandlerTest extends TestCase
{
    use DatabaseTransactions;

    private SkipUnscrambleWordExerciseHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = $this->app->make(SkipUnscrambleWordExerciseHandler::class);
    }

    public function test_handle_skipsExerciseAndUpdatesRatings(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = Exercise::factory()->create([
            'status' => ExerciseStatus::IN_PROGRESS,
            'user_id' => $user->getId(),
        ]);
        $u_exercise = UnscrambleWordExercise::factory()->create(['exercise_id' => $exercise->id]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id]);
        $flashcard = LearningSessionFlashcard::factory()->create(['rating' => null, 'exercise_entry_id' => $entry->id]);

        // WHEN
        $this->handler->handle(new ExerciseId($exercise->id), $user->getId());

        // THEN
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'status' => ExerciseStatus::SKIPPED->value,
        ]);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $flashcard->id,
            'rating' => Rating::UNKNOWN,
            'exercise_entry_id' => $entry->id,
        ]);
    }

    public function test_handle_AssignLatestRatingForFlashcard(): void
    {
        // GIVEN
        $user = $this->createUser();
        $flashcard = LearningSessionFlashcard::factory()->create(['rating' => Rating::WEAK, 'updated_at' => now()->subDay()]);
        LearningSessionFlashcard::factory()->create(['rating' => Rating::GOOD, 'updated_at' => now()->subDay()]);
        $latest_flashcard = LearningSessionFlashcard::factory()->create(['flashcard_id' => $flashcard->flashcard_id, 'rating' => Rating::VERY_GOOD, 'updated_at' => now()]);

        $exercise = Exercise::factory()->create([
            'status' => ExerciseStatus::IN_PROGRESS,
            'user_id' => $user->getId(),
        ]);
        $u_exercise = UnscrambleWordExercise::factory()->create(['exercise_id' => $exercise->id]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id]);
        $flashcard = LearningSessionFlashcard::factory()->create(['rating' => null, 'flashcard_id' => $flashcard->flashcard_id, 'exercise_entry_id' => $entry->id]);

        // WHEN
        $this->handler->handle(new ExerciseId($exercise->id), $user->getId());

        // THEN
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $flashcard->id,
            'rating' => $latest_flashcard->rating,
            'exercise_entry_id' => $entry->id,
        ]);
    }
}
