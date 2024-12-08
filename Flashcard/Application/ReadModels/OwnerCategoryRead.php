<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class OwnerCategoryRead
{
    public function __construct(
        private FlashcardDeckId $id,
        private string $name,
        private LanguageLevel $language_level,
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
}
