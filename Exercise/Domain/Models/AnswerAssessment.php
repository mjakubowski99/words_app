<?php

declare(strict_types=1);

namespace Exercise\Domain\Models;

use Shared\Utils\ValueObjects\ExerciseEntryId;

class AnswerAssessment
{
    public function __construct(
        private ExerciseEntryId $exercise_entry_id,
        private float $score,
        private string $correct_answer,
        private string $user_answer,
    ) {}

    public function getExerciseEntryId(): ExerciseEntryId
    {
        return $this->exercise_entry_id;
    }

    public function isCorrect(): bool
    {
        return $this->score >= 100;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getUserAnswer(): string
    {
        return $this->user_answer;
    }

    public function getCorrectAnswer(): string
    {
        return $this->correct_answer;
    }
}
