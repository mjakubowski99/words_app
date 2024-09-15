<?php

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Domain\ValueObjects\SessionId;

interface IRateableSessionFlashcardsRepository
{
    public function find(SessionId $id): RateableSessionFlashcards;
    public function save(RateableSessionFlashcards $flashcards): void;
}