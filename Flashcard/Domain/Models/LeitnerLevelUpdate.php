<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Types\FlashcardIdCollection;

class LeitnerLevelUpdate
{
    public function __construct(
        private UserId $user_id,
        private FlashcardIdCollection $ids,
        private readonly int $leitner_level_increment_step,
    ) {}

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getFlashcardIds(): FlashcardIdCollection
    {
        return $this->ids;
    }

    public function incrementEasyRatingsCount(): bool
    {
        return $this->leitner_level_increment_step >= Rating::maxLeitnerLevel();
    }

    public function getLeitnerLevelIncrementStep(): int
    {
        return $this->leitner_level_increment_step;
    }
}
