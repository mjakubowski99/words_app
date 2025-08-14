<?php

declare(strict_types=1);

namespace Exercise\Domain\Models;

use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class ExerciseEntry
{
    private bool $updated = false;

    public function __construct(
        private ExerciseEntryId $id,
        private ExerciseId $exercise_id,
        private Answer $correct_answer,
        private ?Answer $last_user_answer,
        private ?bool $last_answer_correct,
        private int $order,
        private float $score = 0.0,
        private int $answers_count = 0,
    ) {}

    public function getId(): ExerciseEntryId
    {
        return $this->id;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getAnswersCount(): int
    {
        return $this->answers_count;
    }

    public function getExerciseId(): ExerciseId
    {
        return $this->exercise_id;
    }

    public function getCorrectAnswer(): Answer
    {
        return $this->correct_answer;
    }

    public function getLastUserAnswer(): ?Answer
    {
        return $this->last_user_answer;
    }

    public function isLastAnswerCorrect(): bool
    {
        return (bool) $this->last_answer_correct;
    }

    public function isUpdated(): bool
    {
        return $this->updated;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setLastUserAnswer(Answer $answer, AnswerAssessment $assessment): void
    {
        $this->last_user_answer = $answer;
        ++$this->answers_count;
        $this->recalculateScoreBasedOnAssessment($assessment);
        $this->setLastUserAnswerCorrect($assessment->isCorrect());
        $this->updated = true;
    }

    private function setLastUserAnswerCorrect(bool $is_correct): void
    {
        $this->last_answer_correct = $is_correct;
        $this->updated = true;
    }

    private function recalculateScoreBasedOnAssessment(AnswerAssessment $assessment): void
    {
        $score = $this->score + $assessment->getRealScore();
        $this->score = $score / $this->answers_count;
    }
}
