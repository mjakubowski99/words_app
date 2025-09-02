<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories;

interface IExerciseStatsRepository
{
    public function getScoreSum(array $exercise_entry_ids): float;
}
