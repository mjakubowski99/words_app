<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Domain\Models\DetailedSessionFlashcard;

interface ISessionFlashcardRepository
{
    public function getLatestSessionFlashcardIds(SessionId $session_id, int $limit): array;

    public function getRatedSessionFlashcardsCount(SessionId $session_id): int;

    public function getTotalSessionFlashcardsCount(SessionId $session_id): int;

    public function getNotRatedFlashcardsInSessionCount(SessionId $id): int;

    /** @return DetailedSessionFlashcard[] */
    public function getNotRatedDetailedSessionFlashcards(SessionId $session_id, int $limit): array;

    public function getNotRatedSessionFlashcards(SessionId $session_id, int $limit): SessionFlashcards;

    public function createMany(SessionFlashcards $flashcards): void;

    public function find(SessionFlashcardId $id);

    public function findMany(SessionId $session_id, array $session_flashcard_ids): SessionFlashcards;

    public function saveRating(SessionFlashcards $session_flashcards): void;
}
