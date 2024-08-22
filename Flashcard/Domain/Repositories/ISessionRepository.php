<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\Models\SessionId;
use Shared\Utils\ValueObjects\UserId;

interface ISessionRepository
{
    /** @return Flashcard[] */
    public function getNotRatedFlashcards(SessionId $session_id): array;
    public function getRatedFlashcardsCount(SessionId $session_id): int;
    public function existsActiveByCategory(UserId $user_id, CategoryId $category_id): bool;
    public function create(Session $session): SessionId;
    public function find(SessionId $id): Session;
}