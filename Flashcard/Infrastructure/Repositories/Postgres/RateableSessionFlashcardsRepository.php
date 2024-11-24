<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Application\Repository\IRateableSessionFlashcardsRepository;
use Flashcard\Infrastructure\Mappers\Postgres\RateableSessionFlashcardsMapper;

class RateableSessionFlashcardsRepository implements IRateableSessionFlashcardsRepository
{
    public function __construct(
        private readonly RateableSessionFlashcardsMapper $mapper
    ) {}

    public function find(SessionId $id): RateableSessionFlashcards
    {
        return $this->mapper->find($id);
    }

    public function save(RateableSessionFlashcards $flashcards): void
    {
        $this->mapper->save($flashcards);
    }
}
