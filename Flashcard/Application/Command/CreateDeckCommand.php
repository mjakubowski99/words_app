<?php

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;
use Shared\Enum\LanguageLevel;

class CreateDeckCommand
{
    public function __construct(
        private Owner $owner,
        private string $tag,
        private string $name,
        private LanguageLevel $default_language_level,
    ) {}

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

    public function getDefaultLanguageLevel(): LanguageLevel
    {
        return $this->default_language_level;
    }
}