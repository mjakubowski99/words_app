<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

class SessionFlashcardRead
{
    public function __construct(
        private readonly SessionFlashcardId $id,
        private readonly string $front_word,
        private readonly Language $front_lang,
        private readonly string $back_word,
        private readonly Language $back_lang,
        private readonly string $front_context,
        private readonly string $back_context,
        private readonly LanguageLevel $language_level,
    ) {}

    public function getId(): SessionFlashcardId
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

    public function getLanguageLevel(): LanguageLevel
    {
        return $this->language_level;
    }
}
