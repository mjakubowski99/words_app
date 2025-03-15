<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Carbon\Carbon;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Shared\Enum\LanguageLevel;

class DeckDetailsRead
{
    public function __construct(
        private FlashcardDeckId $id,
        private string $name,
        private array $flashcards,
        private int $page,
        private int $per_page,
        private int $count,
        private FlashcardOwnerType $owner_type,
        private LanguageLevel $language_level,
        private ?Carbon $last_learnt_at,
        private float $rating_percentage,
    ) {}

    public function getId(): FlashcardDeckId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return FlashcardRead[] */
    public function getFlashcards(): array
    {
        return $this->flashcards;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->per_page;
    }

    public function getFlashcardsCount(): int
    {
        return $this->count;
    }

    public function getOwnerType(): FlashcardOwnerType
    {
        return $this->owner_type;
    }

    public function getLanguageLevel(): LanguageLevel
    {
        return $this->language_level;
    }

    public function getLastLearntAt(): ?Carbon
    {
        return $this->last_learnt_at;
    }

    public function getRatingPercentage(): float
    {
        return $this->rating_percentage;
    }
}
