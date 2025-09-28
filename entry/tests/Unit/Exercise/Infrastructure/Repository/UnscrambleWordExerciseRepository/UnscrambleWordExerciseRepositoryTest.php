<?php

declare(strict_types=1);

use App\Models\Exercise;
use Shared\Models\Emoji;
use Shared\Enum\ExerciseType;
use App\Models\UnscrambleWordExercise;
use App\Models\LearningSessionFlashcard;
use Exercise\Domain\Models\Answer\Answer;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\ExerciseStatus;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\ExerciseEntry\ExerciseEntry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Exercise\Domain\Models\Exercise\UnscrambleWordsExercise;
use Exercise\Infrastructure\Repositories\UnscrambleWordExerciseRepository;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(UnscrambleWordExerciseRepository::class);
});
test('create should create correct exercise', function () {
    // GIVEN
    $user = $this->createUser();
    $session_flashcard_id = LearningSessionFlashcard::factory()->create()->getId();
    $correct_answer = Mockery::mock(Answer::class)->allows([
        'toString' => 'correct answer',
    ]);
    $user_answer = null;
    $entry = Mockery::mock(ExerciseEntry::class)->allows([
        'getId' => ExerciseEntryId::noId(),
        'getCorrectAnswer' => $correct_answer,
        'getLastAnswer' => null,
        'isLastAnswerCorrect' => false,
        'getAnswersCount' => 0,
        'getScore' => 0,
        'getOrder' => 1,
    ]);
    $exercise = Mockery::mock(UnscrambleWordsExercise::class)->allows([
        'getId' => ExerciseId::noId(),
        'getUserId' => $user->getId(),
        'getExerciseEntries' => [$entry],
        'getWord' => 'word',
        'getStatus' => ExerciseStatus::NEW,
        'getContextSentence' => 'context sentence',
        'getContextSentenceTranslation' => 'transaltion',
        'getExerciseType' => ExerciseType::UNSCRAMBLE_WORDS,
        'getScrambledWord' => 'rdow',
        'getWordTranslation' => 'slowo',
        'getEmoji' => Emoji::fromUnicode(';)'),
    ]);

    // WHEN
    $exercise_id = $this->repository->create($exercise);

    // THEN
    $this->assertDatabaseHas('exercises', [
        'id' => $exercise_id->getValue(),
        'user_id' => $user->getId()->getValue(),
        'exercise_type' => $exercise->getExerciseType()->toNumber(),
        'status' => $exercise->getStatus(),
    ]);
    $this->assertDatabaseHas('unscramble_word_exercises', [
        'exercise_id' => $exercise_id->getValue(),
        'word' => $exercise->getWord(),
    ]);
    $this->assertDatabaseHas('exercise_entries', [
        'exercise_id' => $exercise_id->getValue(),
        'correct_answer' => $correct_answer->toString(),
        'answers_count' => $entry->getAnswersCount(),
        'score' => $entry->getScore(),
    ]);
});
test('find should build correct object', function () {
    // GIVEN
    $exercise = Exercise::factory()->create();
    $unscrambled_word_exercise = UnscrambleWordExercise::factory()->create([
        'exercise_id' => $exercise->id,
    ]);
    $exercise_entry = App\Models\ExerciseEntry::factory()->create([
        'exercise_id' => $exercise->id,
        'answers_count' => 3,
        'score' => 0.4,
    ]);

    // WHEN
    $exercise = $this->repository->find(new ExerciseId($exercise->id));

    // THEN
    expect($exercise)->toBeInstanceOf(UnscrambleWordsExercise::class);
    expect($exercise->getExerciseEntries())->toHaveCount(1);
    expect($exercise->getExerciseEntries()[0]->getAnswersCount())->toBe(3);
    expect($exercise->getExerciseEntries()[0]->getScore())->toBe(0.4);
});
