<?php

declare(strict_types=1);

namespace Exercise\Domain\Models\Answer;

use Exercise\Domain\Exceptions\ExerciseAnswerCompareFailureException;
use Exercise\Domain\Models\AnswerAssessment;
use Shared\Utils\ValueObjects\ExerciseEntryId;

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
            $this->getExerciseEntryId(),
            $this->getCompareScore($answer),
            $this->getHintsScore($answer),
            $this->toString(),
            $answer->toString(),
        );
    }

    abstract public function toString(): string;

    abstract protected function getCompareScore(Answer $answer): float;

    abstract protected function getHintsScore(Answer $answer): float;
}
