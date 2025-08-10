<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Http\Resources;

use Exercise\Domain\Models\AnswerAssessment;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property AnswerAssessment[] $resource
 */
class WordMatchExerciseAnswerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'assessments' => array_map(function (AnswerAssessment $assessment) {
                return [
                    'exercise_entry_id' => $assessment->getExerciseEntryId()->getValue(),
                    'is_correct' => $assessment->isCorrect(),
                    'correct_answer' => $assessment->getCorrectAnswer(),
                ];
            }, $this->resource),
        ];
    }
}
