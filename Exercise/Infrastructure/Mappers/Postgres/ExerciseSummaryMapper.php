<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Mappers\Postgres;

use Exercise\Domain\Models\ExerciseStatus;
use Shared\Enum\ExerciseType;
use Illuminate\Support\Facades\DB;
use Shared\Exceptions\NotFoundException;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Application\ReadModels\ExerciseSummary;

class ExerciseSummaryMapper
{
    public function __construct(
        private DB $db,
    ) {}

    public function getExerciseSummaryByFlashcard(int $exercise_entry_id): ExerciseSummary
    {
        $data = $this->db::table('exercise_entries')
            ->where('exercise_entries.id', $exercise_entry_id)
            ->join('exercises', 'exercises.id', '=', 'exercise_entries.exercise_id')
            ->select(['exercises.id', 'exercises.exercise_type', 'exercises.status'])
            ->first();

        if (!$data) {
            throw new NotFoundException('Exercise not found');
        }

        return new ExerciseSummary(
            new ExerciseId($data->id),
            $data->status === ExerciseStatus::DONE->value,
            ExerciseType::from($data->exercise_type),
        );
    }
}
