<?php

declare(strict_types=1);

namespace Exercise\Domain\Models;

use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Exceptions\ExerciseAnswerCompareFailureException;

abstract class Answer
{
    private ExerciseEntryId $answer_entry_id;

    public function __construct(ExerciseEntryId $id)
    {
        $this->answer_entry_id = $id;
    }

    abstract public static function fromString(ExerciseEntryId $id, string $answer): self;

    public function getExerciseEntryId(): ExerciseEntryId
    {
        return $this->answer_entry_id;
    }

    public function compare(Answer $answer): AnswerAssessment
    {
        if ($this->getExerciseEntryId()->getValue() !== $answer->getExerciseEntryId()->getValue()) {
            throw new ExerciseAnswerCompareFailureException(
                'Trying to compare answers for invalid exercise entry id'
            );
        }

        return new AnswerAssessment(
            $this->getCompareScore($answer)
        );
    }

    abstract public function toString(): string;

    abstract protected function getCompareScore(Answer $answer): float;
}
