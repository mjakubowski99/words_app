<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;

interface IFlashcardSelector
{
    public function resetRepetitionsInSession(UserId $user_id): void;

    /** @return Flashcard[] */
    public function select(NextSessionFlashcards $next_session_flashcards, int $limit): array;
}
