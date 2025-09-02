<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Repositories;

use Exercise\Application\Repositories\IExerciseStatsRepository;
use Exercise\Infrastructure\Mappers\Postgres\ExerciseStatsMapper;

class ExerciseStatsRepository implements IExerciseStatsRepository
{
    public function __construct(private ExerciseStatsMapper $mapper) {}

    public function getScoreSum(array $exercise_entry_ids): float
    {
        return $this->mapper->getScoreSum($exercise_entry_ids);
    }
}
