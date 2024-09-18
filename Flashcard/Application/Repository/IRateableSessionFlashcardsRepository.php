<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Models\RateableSessionFlashcards;

interface IRateableSessionFlashcardsRepository
{
    public function find(SessionId $id): RateableSessionFlashcards;

    public function save(RateableSessionFlashcards $flashcards): void;
}
