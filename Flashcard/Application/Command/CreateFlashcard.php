<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class CreateFlashcard
{
    public function __construct(
        private Owner $owner,
        private FlashcardDeckId $deck_id,
        private Language $front_lang,
        private string $front_word,
        private string $front_context,
        private Language $back_lang,
        private string $back_word,
        private string $back_context,
        private LanguageLevel $language_level,
        private ?string $emoji,
    ) {}

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getDeckId(): FlashcardDeckId
    {
        return $this->deck_id;
    }

    public function getFrontLang(): Language
    {
        return $this->front_lang;
    }

    public function getFrontWord(): string
    {
        return $this->front_word;
    }

    public function getFrontContext(): string
    {
        return $this->front_context;
    }

    public function getBackLang(): Language
    {
        return $this->back_lang;
    }

    public function getBackWord(): string
    {
        return $this->back_word;
    }

    public function getBackContext(): string
    {
        return $this->back_context;
    }

    public function getLanguageLevel(): LanguageLevel
    {
        return $this->language_level;
    }

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }
}
