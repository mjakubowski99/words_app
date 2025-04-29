<?php

namespace Exercise\Domain\Models;

use Exercise\Domain\Exceptions\ExerciseAnswerCompareFailureException;
use Exercise\Domain\ValueObjects\ExerciseEntryId;

abstract class Answer
{
    private ExerciseEntryId $answer_entry_id;

    public function __construct(ExerciseEntryId $id)
    {
        $this->answer_entry_id = $id;
    }

    public abstract static function fromString(ExerciseEntryId $id, string $answer): self;

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

    public abstract function toString(): string;

    protected abstract function getCompareScore(Answer $answer): float;
}