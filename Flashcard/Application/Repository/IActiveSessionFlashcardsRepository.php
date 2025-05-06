<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\ActiveSessionFlashcards;

interface IActiveSessionFlashcardsRepository
{
    public function findBySessionFlashcardIds(array $session_flashcard_ids): ActiveSessionFlashcards;

    public function save(ActiveSessionFlashcards $flashcards): void;

    /** @return array<int,Rating> */
    public function findLatestRatings(array $session_flashcard_ids): array;
}
