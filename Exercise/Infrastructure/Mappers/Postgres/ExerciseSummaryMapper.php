<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Mappers\Postgres;

use Shared\Enum\ExerciseType;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Application\ReadModels\ExerciseSummary;

class ExerciseSummaryMapper
{
    public function __construct(
        private DB $db,
    ) {}

    public function getExerciseSummaryByFlashcard(int $session_flashcard_id): ?ExerciseSummary
    {
        $data = $this->db::table('exercise_entries')
            ->where('session_flashcard_id', $session_flashcard_id)
            ->join('exercises', 'exercises.id', '=', 'exercise_entries.exercise_id')
            ->select(['exercises.id', 'exercises.exercise_type'])
            ->first();

        if (!$data) {
            return null;
        }

        return new ExerciseSummary(
            new ExerciseId($data->id),
            ExerciseType::from($data->exercise_type),
            $session_flashcard_id,
        );
    }
}
