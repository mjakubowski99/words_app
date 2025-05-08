<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories;

use Shared\Exercise\IExerciseSummary;

interface IExerciseSummaryRepository
{
    public function getExerciseSummaryByFlashcard(int $exercise_entry_id): ?IExerciseSummary;
}
