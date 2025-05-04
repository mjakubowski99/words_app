<?php

declare(strict_types=1);

namespace Tests\Integration\Exercise\Application\Command\AnswerUnscrambleWordExerciseHandler;

use Tests\TestCase;
use App\Models\Exercise;
use App\Models\ExerciseEntry;
use Shared\Enum\SessionStatus;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\UnscrambleWordExercise;
use App\Models\LearningSessionFlashcard;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\Models\UnscrambleWordAnswer;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Exercise\Application\Command\UnscrambleWordExerciseAnswerHandler;

class UnscrambleWordAnswerExerciseHandlerTest extends TestCase
{
    use DatabaseTransactions;

    private UnscrambleWordExerciseAnswerHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(UnscrambleWordExerciseAnswerHandler::class);
    }

    public function test__handle_validInput_updatesExerciseAndReturnsAssessment(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = Exercise::factory()->create(['status' => ExerciseStatus::NEW, 'user_id' => $user->getId()]);
        $u_exercise = UnscrambleWordExercise::factory()->create(['exercise_id' => $exercise->id]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id]);
        $entry_id = new ExerciseEntryId($entry->id);

        // WHEN
        $assessment = $this->handler->handle(
            new ExerciseEntryId($entry->id),
            $user->getId(),
            UnscrambleWordAnswer::fromString($entry_id, $u_exercise->word),
        );

        // THEN
        $this->assertTrue($assessment->isCorrect());
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $entry->session_flashcard_id,
            'rating' => Rating::VERY_GOOD,
        ]);
    }

    public function test__handle_invalidInput_doNotUpdateExercise(): void
    {
        // GIVEN
        $user = $this->createUser();
        $session_flashcard = LearningSessionFlashcard::factory()->create(['rating' => null]);
        $exercise = Exercise::factory()->create(['status' => ExerciseStatus::NEW, 'user_id' => $user->getId()]);
        $u_exercise = UnscrambleWordExercise::factory()->create(['exercise_id' => $exercise->id]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id, 'session_flashcard_id' => $session_flashcard->id]);
        $entry_id = new ExerciseEntryId($entry->id);

        // WHEN
        $assessment = $this->handler->handle(
            new ExerciseEntryId($entry->id),
            $user->getId(),
            UnscrambleWordAnswer::fromString($entry_id, 'invalid answer'),
        );

        // THEN
        $this->assertFalse($assessment->isCorrect());
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $entry->session_flashcard_id,
            'rating' => null,
        ]);
    }

    public function test__handle_learningSessionFinished_doNotUpdateRating(): void
    {
        // GIVEN
        $user = $this->createUser();
        $session = LearningSession::factory()->create(['status' => SessionStatus::FINISHED]);
        $session_flashcard = LearningSessionFlashcard::factory()->create(['learning_session_id' => $session->id, 'rating' => null]);
        $exercise = Exercise::factory()->create(['status' => ExerciseStatus::NEW, 'user_id' => $user->getId()]);
        $u_exercise = UnscrambleWordExercise::factory()->create(['exercise_id' => $exercise->id]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id, 'session_flashcard_id' => $session_flashcard->id]);
        $entry_id = new ExerciseEntryId($entry->id);

        // WHEN
        $assessment = $this->handler->handle(
            new ExerciseEntryId($entry->id),
            $user->getId(),
            UnscrambleWordAnswer::fromString($entry_id, $u_exercise->word),
        );

        // THEN
        $this->assertTrue($assessment->isCorrect());
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $entry->session_flashcard_id,
            'rating' => null,
        ]);
    }
}
