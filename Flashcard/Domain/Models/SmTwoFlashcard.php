<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardId;

class SmTwoFlashcard
{
    public const INITIAL_REPETITION_RATIO = 2.5;
    public const INITIAL_REPETITION_INTERVAL = 1;

    private float $repetition_ratio;
    private float $repetition_interval;
    private int $repetition_count;

    public function __construct(
        private UserId $user_id,
        private FlashcardId $flashcard_id,
        ?float $repetition_ratio = null,
        ?float $repetition_interval = null,
        ?int $repetition_count = null,
    ) {
        $this->repetition_ratio = $repetition_ratio ?? self::INITIAL_REPETITION_RATIO;
        $this->repetition_interval = $repetition_interval ?? self::INITIAL_REPETITION_INTERVAL;
        $this->repetition_count = $repetition_count ?? 0;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getFlashcardId(): FlashcardId
    {
        return $this->flashcard_id;
    }

    public function getRepetitionInterval(): float
    {
        return $this->repetition_interval;
    }

    public function getRepetitionRatio(): float
    {
        return $this->repetition_ratio;
    }

    public function getRepetitionCount(): int
    {
        return $this->repetition_count;
    }

    public function updateByRating(Rating $rating): void
    {
        $this->calculateRepetitionInterval($rating);

        $this->calculateRepetitionRatio($rating);
    }

    private function calculateRepetitionInterval(Rating $rating): void
    {
        if ($rating->value >= Rating::GOOD->value) {
            if ($this->repetition_count === 0) {
                $this->repetition_interval = $rating === Rating::GOOD ? 1.0 : 6.0;
            } elseif ($this->repetition_count === 1) {
                $this->repetition_interval = 6.0;
            } else {
                $this->repetition_interval = $this->repetition_interval * $this->repetition_ratio;
            }
            ++$this->repetition_count;
        } else {
            $this->repetition_interval = 1;
            $this->repetition_count = 0;  // Zresetuj licznik powtórzeń po złej odpowiedzi
        }
    }

    private function calculateRepetitionRatio(Rating $rating): void
    {
        $this->repetition_ratio = $this->repetition_ratio + (0.1 - (3 - $rating->value) * (0.08 + (3 - $rating->value) * 0.02));

        $this->repetition_ratio = round($this->repetition_ratio, 6);

        if ($this->repetition_ratio < 1.3) {
            $this->repetition_ratio = 1.3;
        }
    }
}
