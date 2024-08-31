<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Models\SessionId;
use Shared\Utils\ValueObjects\UserId;

interface ISessionFlashcardRepository
{
    public function getNotRatedSessionFlashcards(SessionId $session_id, int $limit): SessionFlashcards;
    /** @param Flashcard[] $flashcards */
    public function addFlashcardsToSession(SessionId $session_id, array $flashcards): void;
    public function findSessionFlashcard(SessionFlashcardId $id);
    public function findMany(UserId $user_id, array $session_flashcard_ids): SessionFlashcards;
    public function saveRating(SessionFlashcards $session_flashcards): void;
}
