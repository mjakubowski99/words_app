<?php

declare(strict_types=1);

namespace Exercise\Domain\Models;

use Shared\Utils\ValueObjects\ExerciseEntryId;

class UnscrambleWordAnswer extends Answer
{
    public function __construct(
        ExerciseEntryId $id,
        private string $unscrambled_word,
    ) {
        parent::__construct($id);
    }

    public static function fromString(ExerciseEntryId $id, string $answer): Answer
    {
        return new UnscrambleWordAnswer($id, $answer);
    }

    public function toString(): string
    {
        return $this->unscrambled_word;
    }

    protected function getCompareScore(Answer $answer): float
    {
        if (mb_strtolower($answer->toString()) === mb_strtolower($this->toString())) {
            return 100.0;
        }

        return 0.0;
    }
}
