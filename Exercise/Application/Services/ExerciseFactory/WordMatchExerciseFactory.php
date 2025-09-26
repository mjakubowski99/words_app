<?php

declare(strict_types=1);

namespace Exercise\Application\Services\ExerciseFactory;

use Exercise\Application\DTO\FlashcardExercise;
use Exercise\Application\Repositories\Exercise\IWordMatchExerciseRepository;
use Exercise\Domain\Models\Exercise\WordMatchExercise;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Utils\ValueObjects\UserId;

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
