<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\SessionId;
use Shared\Utils\ValueObjects\UserId;

interface IFlashcardRepository
{
    /** @return Flashcard[] */
    public function getFlashcardsWithLowestRepetitionInterval(UserId $user_id, CategoryId $category_id, int $limit): array;

    /** @return Flashcard[] */
    public function getNotRatedSessionFlashcards(SessionId $session_id, int $limit): array;

    public function addFlashcardsToSession(SessionId $session_id, array $flashcards): void;
}
