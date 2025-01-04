<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\UserId;

class GenerateFlashcards
{
    public function __construct(
        private readonly UserId $user_id,
        private readonly string $deck_name,
        private readonly LanguageLevel $language_level,
    ) {}

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getDeckName(): string
    {
        return $this->deck_name;
    }

    public function getLanguageLevel(): LanguageLevel
    {
        return $this->language_level;
    }
}
