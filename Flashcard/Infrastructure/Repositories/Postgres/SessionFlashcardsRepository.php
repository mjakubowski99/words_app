<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcardCollection;
use Flashcard\Application\Repository\ISessionFlashcardsRepository;
use Flashcard\Infrastructure\Mappers\Postgres\SessionFlashcardsMapper;

class SessionFlashcardsRepository implements ISessionFlashcardsRepository
{
    public function __construct(
        private readonly SessionFlashcardsMapper $mapper
    ) {}

    public function findBySessionFlashcardIds(array $session_flashcard_ids): SessionFlashcardCollection
    {
        return $this->mapper->findBySessionFlashcardIds($session_flashcard_ids);
    }

    public function save(SessionFlashcardCollection $flashcards): void
    {
        $this->mapper->save($flashcards);
    }

    /** @return array<int,Rating> */
    public function findLatestRatings(array $session_flashcard_ids): array
    {
        return $this->mapper->findLatestRatings($session_flashcard_ids);
    }
}
