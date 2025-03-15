<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

interface ISmTwoFlashcardRepository
{
    public function resetRepetitionsInSession(UserId $user_id): void;

    public function findMany(UserId $user_id, array $flashcard_ids): SmTwoFlashcards;

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void;

    /**
     * @param  FlashcardSortCriteria[] $sort_criteria
     * @return Flashcard[]
     */
    public function getNextFlashcardsByDeck(
        UserId $user_id,
        FlashcardDeckId $deck_id,
        int $limit,
        array $exclude_flashcard_ids,
        array $sort_criteria,
        int $cards_per_session,
        bool $from_poll,
    ): array;

    /**
     * @param  FlashcardSortCriteria[] $sort_criteria
     * @return Flashcard[]
     */
    public function getNextFlashcardsByUser(
        UserId $user_id,
        int $limit,
        array $exclude_flashcard_ids,
        array $sort_criteria,
        int $cards_per_session,
        bool $from_poll,
        bool $exclude_from_poll,
    ): array;
}
