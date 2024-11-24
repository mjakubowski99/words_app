<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

interface ISmTwoFlashcardRepository
{
    public function findMany(Owner $owner, array $flashcard_ids): SmTwoFlashcards;

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void;

    /**
     * @param  FlashcardSortCriteria[] $sort_criteria
     * @return Flashcard[]
     */
    public function getNextFlashcardsByDeck(FlashcardDeckId $deck_id, int $limit, array $exclude_flashcard_ids, array $sort_criteria): array;

    /**
     * @param  FlashcardSortCriteria[] $sort_criteria
     * @return Flashcard[]
     */
    public function getNextFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids, array $sort_criteria): array;
}
