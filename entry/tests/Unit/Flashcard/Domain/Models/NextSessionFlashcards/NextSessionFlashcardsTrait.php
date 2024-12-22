<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\NextSessionFlashcards;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\OwnerId;
use Shared\Enum\FlashcardOwnerType;
use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\Language;
use Shared\Utils\ValueObjects\Uuid;

trait NextSessionFlashcardsTrait
{
    private function makeCategory(Owner $owner): Deck
    {
        return new Deck($owner, 'tag', 'name', LanguageLevel::A2);
    }

    private function makeOwner(): Owner
    {
        return new Owner(new OwnerId(Uuid::make()->getValue()), FlashcardOwnerType::USER);
    }

    private function makeFlashcard(Owner $owner): Flashcard
    {
        return new Flashcard(
            new FlashcardId(1),
            'word',
            Language::pl(),
            'trans',
            Language::en(),
            'context',
            'context_translation',
            $owner,
            null,
            LanguageLevel::A1,
        );
    }
}
