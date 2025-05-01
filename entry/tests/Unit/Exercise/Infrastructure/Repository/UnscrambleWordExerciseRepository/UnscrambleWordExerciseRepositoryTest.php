<?php

namespace Tests\Unit\Exercise\Infrastructure\Repository\UnscrambleWordExerciseRepository;

use App\Models\Exercise;
use App\Models\LearningSessionFlashcard;
use App\Models\UnscrambleWordExercise;
use Exercise\Domain\Models\Answer;
use Exercise\Domain\Models\ExerciseEntry;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Domain\ValueObjects\SessionFlashcardId;
use Exercise\Infrastructure\Repositories\UnscrambleWordExerciseRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\ExerciseId;
use Tests\TestCase;

class UnscrambleWordExerciseRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private UnscrambleWordExerciseRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(UnscrambleWordExerciseRepository::class);
    }

    public function test__create_ShouldCreateCorrectExercise(): void
    {
        // GIVEN
        $user = $this->createUser();
        $session_flashcard_id = LearningSessionFlashcard::factory()->create()->getId();
        $correct_answer = \Mockery::mock(Answer::class)->allows([
            'toString' => 'correct answer',
        ]);
        $user_answer = null;
        $entry = \Mockery::mock(ExerciseEntry::class)->allows([
            'getId' => ExerciseEntryId::noId(),
            'getSessionFlashcardId' => new SessionFlashcardId($session_flashcard_id->getValue()),
            'getCorrectAnswer' => $correct_answer,
            'getLastAnswer' => null,
            'isLastAnswerCorrect' => false,
        ]);
        $exercise = \Mockery::mock(UnscrambleWordsExercise::class)->allows([
            'getId' => ExerciseId::noId(),
            'getUserId' => $user->getId(),
            'getExerciseEntries' => [$entry],
            'getWord' => 'word',
            'getStatus' => ExerciseStatus::NEW,
            'getContextSentence' => 'context sentence',
            'getExerciseType' => ExerciseType::UNSCRAMBLE_WORDS,
            'getScrambledWord' => 'rdow',
            'getWordTranslation' => 'slowo',
            'getEmoji' => ';)',
        ]);

        // WHEN
        $exercise_id = $this->repository->create($exercise);

        // THEN
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise_id->getValue(),
            'user_id' => $user->getId()->getValue(),
            'exercise_type' => $exercise->getExerciseType(),
            'status' => $exercise->getStatus(),
        ]);
        $this->assertDatabaseHas('unscramble_word_exercises', [
            'exercise_id' => $exercise_id->getValue(),
            'word' => $exercise->getWord(),
        ]);
        $this->assertDatabaseHas('exercise_entries', [
            'exercise_id' => $exercise_id->getValue(),
            'correct_answer' => $correct_answer->toString(),
        ]);
    }

    public function test__find_ShouldBuildCorrectObject(): void
    {
        // GIVEN
        $exercise = Exercise::factory()->create();
        $unscrambled_word_exercise = UnscrambleWordExercise::factory()->create([
            'exercise_id' => $exercise->id,
        ]);
        $exercise_entry = \App\Models\ExerciseEntry::factory()->create([
            'exercise_id' => $exercise->id,
        ]);

        // WHEN
        $exercise = $this->repository->find(new ExerciseId($exercise->id));

        // THEN
        $this->assertInstanceOf(UnscrambleWordsExercise::class, $exercise);
    }
}
