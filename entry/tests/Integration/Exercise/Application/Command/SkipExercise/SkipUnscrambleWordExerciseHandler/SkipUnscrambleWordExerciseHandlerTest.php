<?php

declare(strict_types=1);

use App\Models\Exercise;
use App\Models\ExerciseEntry;
use Flashcard\Domain\Models\Rating;
use App\Models\UnscrambleWordExercise;
use App\Models\LearningSessionFlashcard;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\ExerciseStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Exercise\Application\Command\SkipExercise\SkipUnscrambleWordExerciseHandler;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->handler = $this->app->make(SkipUnscrambleWordExerciseHandler::class);
});
test('handle skips exercise and updates ratings', function () {
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
});
test('handle updates to hard rating', function () {
    // GIVEN
    $user = $this->createUser();
    $flashcard = LearningSessionFlashcard::factory()->create(['rating' => null, 'updated_at' => now()->subDay()]);
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
        'rating' => Rating::UNKNOWN->value,
        'exercise_entry_id' => $entry->id,
    ]);
});
