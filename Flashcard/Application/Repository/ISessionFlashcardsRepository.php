<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcardCollection;

interface ISessionFlashcardsRepository
{
    public function findBySessionFlashcardIds(array $session_flashcard_ids): SessionFlashcardCollection;

    public function save(SessionFlashcardCollection $flashcards): void;

    /** @return array<int,Rating> */
    public function findLatestRatings(array $session_flashcard_ids): array;
}
