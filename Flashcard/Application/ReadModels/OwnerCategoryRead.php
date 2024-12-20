<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Carbon\Carbon;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class OwnerCategoryRead
{
    public function __construct(
        private FlashcardDeckId $id,
        private string $name,
        private LanguageLevel $language_level,
        private int $flashcards_count,
        private float $rating_ratio,
        private ?Carbon $last_learnt_at,
    ) {}

    public function getId(): FlashcardDeckId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLanguageLevel(): LanguageLevel
    {
        return $this->language_level;
    }

    public function getFlashcardsCount(): int
    {
        return $this->flashcards_count;
    }

    public function getRatingRatio(): float
    {
        return $this->rating_ratio;
    }

    public function getLastLearntAt(): ?Carbon
    {
        return $this->last_learnt_at;
    }
}
