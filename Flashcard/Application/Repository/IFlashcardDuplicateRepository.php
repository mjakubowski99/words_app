<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\ValueObjects\FlashcardDeckId;

interface IFlashcardDuplicateRepository
{
    public function getAlreadySavedFrontWords(FlashcardDeckId $deck_id, array $front_words): array;

    public function getRandomFrontWordInitialLetters(FlashcardDeckId $deck_id, int $limit): array;
}
