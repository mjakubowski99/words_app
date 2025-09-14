<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Shared\Enum\Language;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;

interface IFlashcardSelector
{
    public function resetRepetitionsInSession(UserId $user_id): void;

    /** @return Flashcard[] */
    public function selectToPoll(UserId $user_id, int $limit, Language $front, Language $back, array $exclude_flashcard_ids = []): array;

    /** @return Flashcard[] */
    public function select(NextSessionFlashcards $next_session_flashcards, int $limit, Language $front, Language $back, array $exclude_flashcard_ids = []): array;

    /** @return Flashcard[] */
    public function selectFromPoll(NextSessionFlashcards $next_session_flashcards, int $limit, Language $front, Language $back, array $exclude_flashcard_ids = []): array;
}
