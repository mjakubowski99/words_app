<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Repositories;

use Shared\Exercise\IExerciseSummary;
use Exercise\Application\Repositories\IExerciseSummaryRepository;
use Exercise\Infrastructure\Mappers\Postgres\ExerciseSummaryMapper;

class ExerciseSummaryRepository implements IExerciseSummaryRepository
{
    public function __construct(
        private ExerciseSummaryMapper $mapper
    ) {}

    public function getExerciseSummaryByFlashcard(int $exercise_entry_id): IExerciseSummary
    {
        return $this->mapper->getExerciseSummaryByFlashcard($exercise_entry_id);
    }
}
