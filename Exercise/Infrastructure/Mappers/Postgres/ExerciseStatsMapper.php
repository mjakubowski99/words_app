<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Mappers\Postgres;

use Illuminate\Support\Facades\DB;

class ExerciseStatsMapper
{
    public function getScoreSum(array $exercise_entry_ids): float
    {
        $score = DB::table('exercise_entries')
            ->whereIn('id', $exercise_entry_ids)
            ->sum('score');

        return $score ? (float) $score : 0.0;
    }
}
