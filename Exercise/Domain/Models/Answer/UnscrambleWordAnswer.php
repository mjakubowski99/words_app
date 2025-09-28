<?php

declare(strict_types=1);

namespace Exercise\Domain\Models\Answer;

use Shared\Utils\ValueObjects\ExerciseEntryId;

class UnscrambleWordAnswer extends Answer
{
    private int $hints_count = 0;

    public function __construct(
        ExerciseEntryId $id,
        private string $unscrambled_word,
    ) {
        parent::__construct($id);
    }

    public function setHintsCount(int $hints_count): void
    {
        $this->hints_count = $hints_count;
    }

    public static function fromString(ExerciseEntryId $id, string $answer): self
    {
        return new UnscrambleWordAnswer($id, $answer);
    }

    public static function fromStringWithHints(ExerciseEntryId $id, string $answer, int $hints_count): self
    {
        $answer = new UnscrambleWordAnswer($id, $answer);
        $answer->setHintsCount($hints_count);

        return $answer;
    }

    public function getHintsCount(): int
    {
        return $this->hints_count;
    }

    public function toString(): string
    {
        return $this->unscrambled_word;
    }

    protected function getCompareScore(Answer $answer): float
    {
        if (!$answer instanceof UnscrambleWordAnswer) {
            throw new \UnexpectedValueException('Expected UnscrambleWordAnswer instance for comparison.');
        }

        $correct = mb_strtolower($answer->toString()) === mb_strtolower($this->toString());

        return $correct ? 100.0 : 0.0;
    }

    protected function getHintsScore(Answer $answer): float
    {
        if (!$answer instanceof UnscrambleWordAnswer) {
            throw new \UnexpectedValueException('Expected UnscrambleWordAnswer instance for comparison.');
        }

        return round($answer->getHintsCount() / mb_strlen($answer->toString()) * 100, 2);
    }
}
