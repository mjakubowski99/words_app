<?php

namespace Exercise\Domain\Models;

use Exercise\Domain\ValueObjects\AnswerEntryId;

class UnscrambleWordAnswer extends Answer
{
    public function __construct(
        AnswerEntryId $id,
        private string $unscrambled_word,
    ) {
        parent::__construct($id);
    }

    public static function fromString(AnswerEntryId $id, string $answer): Answer
    {
        return new UnscrambleWordAnswer($id, $answer);
    }

    public function toString(): string
    {
        return $this->unscrambled_word;
    }

    protected function getCompareScore(Answer $answer): float
    {
        if ($answer->toString() === $this->toString()) {
            return 100.0;
        }
        return 0.0;
    }
}