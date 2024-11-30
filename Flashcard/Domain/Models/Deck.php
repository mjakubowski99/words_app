<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class Deck
{
    private FlashcardDeckId $id;

    public function __construct(
        private Owner $owner,
        private string $tag,
        private string $name,
        private LanguageLevel $default_language_level,
    ) {}

    public function init(FlashcardDeckId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): FlashcardDeckId
    {
        return $this->id;
    }

    public function hasOwner(): bool
    {
        return true;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDefaultLanguageLevel(): LanguageLevel
    {
        return $this->default_language_level;
    }
}
