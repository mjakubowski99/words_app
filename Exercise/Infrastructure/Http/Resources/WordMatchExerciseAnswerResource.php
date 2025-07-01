<?php

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
                    'exercise_entry_id' => $assessment->getExerciseEntryId(),
                    'is_correct' => $assessment->isCorrect(),
                    'correct_answer' => $assessment->getCorrectAnswer(),
                ];
            }, $this->resource),
        ];
    }
}