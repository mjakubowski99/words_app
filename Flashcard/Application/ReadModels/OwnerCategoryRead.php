<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Shared\Enum\LanguageLevel;

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
