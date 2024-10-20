<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\CategoryId;

class CreateFlashcard
{
    public function __construct(
        private Owner $owner,
        private CategoryId $category_id,
        private Language $word_lang,
        private string $word,
        private string $context,
        private Language $translation_lang,
        private string $translation,
        private string $context_translation,
    ) {}

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getCategoryId(): CategoryId
    {
        return $this->category_id;
    }

    public function getWordLang(): Language
    {
        return $this->word_lang;
    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getTranslationLang(): Language
    {
        return $this->translation_lang;
    }

    public function getTranslation(): string
    {
        return $this->translation;
    }

    public function getContextTranslation(): string
    {
        return $this->context_translation;
    }
}
