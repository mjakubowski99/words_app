<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Services\FlashcardDuplicateService;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;

trait FlashcardDuplicateServiceTrait
{
    private function createFlashcard(string $front_word): Flashcard
    {
        return new Flashcard(
            FlashcardId::noId(),
            $front_word,
            Language::pl(),
            'back',
            Language::en(),
            'context',
            'back context',
            null,
            null,
            LanguageLevel::B1,
            null,
        );
    }
}
