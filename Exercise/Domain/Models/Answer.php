<?php

namespace Exercise\Domain\Models;

use Exercise\Domain\ValueObjects\AnswerEntryId;

abstract class Answer
{
    private AnswerEntryId $answer_entry_id;

    public function __construct(AnswerEntryId $id)
    {
        $this->answer_entry_id = $id;
    }

    public abstract static function fromString(AnswerEntryId $id, string $answer): self;

    public function getAnswerEntryId(): AnswerEntryId
    {
        return $this->answer_entry_id;
    }

    public function compare(Answer $answer): AnswerAssessment
    {
        if ($this->getAnswerEntryId() !== $answer->getAnswerEntryId()) {
            throw new \UnexpectedValueException('AnswerEntryId mismatch');
        }
        return new AnswerAssessment(
            $this->getCompareScore($answer)
        );
    }

    public abstract function toString(): string;

    protected abstract function getCompareScore(Answer $answer): float;
}