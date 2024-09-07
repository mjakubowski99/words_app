<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Infrastructure\Mappers\SessionFlashcardMapper;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;
use Flashcard\Infrastructure\Mappers\DetailedSessionFlashcardMapper;

class SessionFlashcardRepository implements ISessionFlashcardRepository
{
    public function __construct(
        private readonly SessionFlashcardMapper $session_flashcard_mapper,
        private readonly DetailedSessionFlashcardMapper $detailed_flashcard_mapper,
    ) {}

    public function getNotRatedDetailedSessionFlashcards(SessionId $session_id, int $limit): array
    {
        return $this->detailed_flashcard_mapper->getNotRatedSessionFlashcards($session_id, $limit);
    }

    public function createMany(SessionFlashcards $flashcards): void
    {
        $this->session_flashcard_mapper->createMany($flashcards);
    }

    public function find(SessionFlashcardId $id): SessionFlashcard
    {
        return $this->session_flashcard_mapper->find($id);
    }

    public function findMany(SessionId $session_id, array $session_flashcard_ids): SessionFlashcards
    {
        return $this->session_flashcard_mapper->findMany($session_id, $session_flashcard_ids);
    }

    public function saveRating(SessionFlashcards $session_flashcards): void
    {
        $this->session_flashcard_mapper->saveRating($session_flashcards);
    }

    public function getRatedSessionFlashcardsCount(SessionId $session_id): int
    {
        return $this->session_flashcard_mapper->getRatedSessionFlashcardsCount($session_id);
    }

    public function getTotalSessionFlashcardsCount(SessionId $session_id): int
    {
        return $this->session_flashcard_mapper->getRatedSessionFlashcardsCount($session_id);
    }

    public function getNotRatedFlashcardsInSessionCount(SessionId $id): int
    {
        return $this->session_flashcard_mapper->getNotRatedSessionFlashcardsCount($id);
    }

    public function getNotRatedSessionFlashcards(SessionId $session_id, int $limit): SessionFlashcards
    {
        return $this->session_flashcard_mapper->findFlashcardWithNullRating($session_id, $limit);
    }
}
