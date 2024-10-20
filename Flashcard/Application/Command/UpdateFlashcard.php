<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;

class UpdateFlashcard extends CreateFlashcard
{
    public function __construct(
        private readonly FlashcardId $id,
        Owner $owner,
        CategoryId $category_id,
        Language $word_lang,
        string $word,
        string $context,
        Language $translation_lang,
        string $translation,
        string $context_translation,
    ) {
        parent::__construct(
            $owner,
            $category_id,
            $word_lang,
            $word,
            $context,
            $translation_lang,
            $translation,
            $context_translation
        );
    }

    public function getId(): FlashcardId
    {
        return $this->id;
    }
}
