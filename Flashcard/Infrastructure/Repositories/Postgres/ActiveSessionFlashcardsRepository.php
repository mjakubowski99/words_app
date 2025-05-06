<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\ActiveSessionFlashcards;
use Flashcard\Application\Repository\IActiveSessionFlashcardsRepository;
use Flashcard\Infrastructure\Mappers\Postgres\ActiveSessionFlashcardsMapper;

class ActiveSessionFlashcardsRepository implements IActiveSessionFlashcardsRepository
{
    public function __construct(
        private readonly ActiveSessionFlashcardsMapper $mapper
    ) {}

    public function findBySessionFlashcardIds(array $session_flashcard_ids): ActiveSessionFlashcards
    {
        return $this->mapper->findBySessionFlashcardIds($session_flashcard_ids);
    }

    public function save(ActiveSessionFlashcards $flashcards): void
    {
        $this->mapper->save($flashcards);
    }

    /** @return array<int,Rating> */
    public function findLatestRatings(array $session_flashcard_ids): array
    {
        return $this->mapper->findLatestRatings($session_flashcard_ids);
    }
}
