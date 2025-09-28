<?php

declare(strict_types=1);

namespace Exercise\Application\Facades;

use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Shared\Exercise\Exercises\IWordMatchExerciseRead;
use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;
use Exercise\Application\Repositories\IExerciseStatsRepository;
use Exercise\Application\Repositories\ExerciseRead\IWordMatchExerciseReadRepository;
use Exercise\Application\Repositories\ExerciseRead\IUnscrambleWordExerciseReadRepository;

class ExerciseReadFacade implements IExerciseReadFacade
{
    public function __construct(
        private IUnscrambleWordExerciseReadRepository $unscramble_word_repository,
        private IWordMatchExerciseReadRepository $word_match_repository,
        private IExerciseStatsRepository $exercise_stats_repository,
    ) {}

    public function getExerciseScoreSum(array $exercise_entry_ids): float
    {
        return $this->exercise_stats_repository->getScoreSum($exercise_entry_ids);
    }

    public function getUnscrambleWordExercise(ExerciseEntryId $id): IUnscrambleWordExerciseRead
    {
        return $this->unscramble_word_repository->findByEntryId($id);
    }

    public function getWordMatchExercise(ExerciseEntryId $id): IWordMatchExerciseRead
    {
        return $this->word_match_repository->findByEntryId($id);
    }
}
