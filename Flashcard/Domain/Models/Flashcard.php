<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;

final class Flashcard
{
    private Language $learned_language;

    /**
     * @throws \Exception
     */
    public function __construct(
        private FlashcardId $id,
        private string $front_word,
        private Language $front_lang,
        private string $back_word,
        private Language $back_lang,
        private string $front_context,
        private string $back_context,
        private ?Owner $owner,
        private ?Deck $deck,
        private LanguageLevel $level,
    ) {
        $this->learned_language = Language::from($this->back_lang->getValue());

        if (!in_array($this->level, $this->learned_language->getAvailableLanguages())) {
            throw new \Exception("Invalid language level: {$this->level->value} for language {$this->learned_language}");
        }
    }

    public function getId(): FlashcardId
    {
        return $this->id;
    }

    public function getFrontWord(): string
    {
        return $this->front_word;
    }

    public function getFrontLang(): Language
    {
        return $this->front_lang;
    }

    public function getBackWord(): string
    {
        return $this->back_word;
    }

    public function getBackLang(): Language
    {
        return $this->back_lang;
    }

    public function getFrontContext(): string
    {
        return $this->front_context;
    }

    public function getBackContext(): string
    {
        return $this->back_context;
    }

    public function hasOwner(): bool
    {
        return $this->owner !== null;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function hasDeck(): bool
    {
        return $this->deck !== null;
    }

    public function getDeck(): Deck
    {
        return $this->deck;
    }

    public function getLearnedLanguage(): Language
    {
        return $this->learned_language;
    }

    public function getLanguageLevel(): LanguageLevel
    {
        return $this->level;
    }
}
