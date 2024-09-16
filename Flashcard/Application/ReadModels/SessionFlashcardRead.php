<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

class SessionFlashcardRead
{
    public function __construct(
        private readonly SessionFlashcardId $id,
        private readonly string $word,
        private readonly Language $word_lang,
        private readonly string $translation,
        private readonly Language $translation_lang,
        private readonly string $context,
        private readonly string $context_translation,
    ) {}

    public function getId(): SessionFlashcardId
    {
        return $this->id;
    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function getWordLang(): Language
    {
        return $this->word_lang;
    }

    public function getTranslation(): string
    {
        return $this->translation;
    }

    public function getTranslationLang(): Language
    {
        return $this->translation_lang;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getContextTranslation(): string
    {
        return $this->context_translation;
    }
}
