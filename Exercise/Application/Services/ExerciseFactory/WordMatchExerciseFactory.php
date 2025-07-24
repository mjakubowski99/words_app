<?php

declare(strict_types=1);

namespace Exercise\Application\Services\ExerciseFactory;

use Shared\Utils\ValueObjects\UserId;
use Exercise\Domain\Models\WordMatchExercise;
use Exercise\Application\DTO\FlashcardExercise;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Exercise\Application\Repositories\IWordMatchExerciseRepository;

class WordMatchExerciseFactory implements IExerciseFactory
{
    public function __construct(
        private IWordMatchExerciseRepository $word_match_exercise_repository,
    ) {}

    public function make(ISessionFlashcardSummaries $summaries, UserId $user_id): array
    {
        $exercise = WordMatchExercise::newFromSummaries($summaries, $user_id);

        $exercise_id = $this->word_match_exercise_repository->create($exercise);

        $exercise = $this->word_match_exercise_repository->find($exercise_id);

        return FlashcardExercise::fromFlashcardSummaries($summaries, $exercise);
    }
}
