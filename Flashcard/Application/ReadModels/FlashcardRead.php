<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;

class FlashcardRead
{
    public function __construct(
        private FlashcardId $id,
        private string $word,
        private Language $word_lang,
        private string $translation,
        private Language $translation_lang,
        private string $context,
        private string $context_translation,
    ) {}

    public function getId(): FlashcardId
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
