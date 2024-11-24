<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Factories\Postgres;

use Flashcard\Application\Repository\FlashcardSortCriteria;
use Flashcard\Infrastructure\SortCriteria\Postgres\HardFlashcardsFirst;
use Flashcard\Infrastructure\SortCriteria\Postgres\PostgresSortCriteria;
use Flashcard\Infrastructure\SortCriteria\Postgres\NotHardFlashcardsFirst;
use Flashcard\Infrastructure\SortCriteria\Postgres\NotRatedFlashcardsFirst;
use Flashcard\Infrastructure\SortCriteria\Postgres\OldestUpdateFlashcardsFirst;
use Flashcard\Infrastructure\SortCriteria\Postgres\RandomizeLatestFlashcardOrder;
use Flashcard\Infrastructure\SortCriteria\Postgres\PlannedFlashcardsForCurrentDateFirst;
use Flashcard\Infrastructure\SortCriteria\Postgres\LowestRepetitionIntervalFlashcardFirst;

class FlashcardSortCriteriaFactory
{
    public function make(FlashcardSortCriteria $criteria_type): PostgresSortCriteria
    {
        return match ($criteria_type) {
            FlashcardSortCriteria::NOT_RATED_FLASHCARDS_FIRST => new NotRatedFlashcardsFirst(),
            FlashcardSortCriteria::LOWEST_REPETITION_INTERVAL_FIRST => new LowestRepetitionIntervalFlashcardFirst(),
            FlashcardSortCriteria::PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST => new PlannedFlashcardsForCurrentDateFirst(),
            FlashcardSortCriteria::RANDOMIZE_LATEST_FLASHCARDS_ORDER => new RandomizeLatestFlashcardOrder(),
            FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST => new OldestUpdateFlashcardsFirst(),
            FlashcardSortCriteria::NOT_HARD_FLASHCARDS_FIRST => new NotHardFlashcardsFirst(),
            FlashcardSortCriteria::HARD_FLASHCARDS_FIRST => new HardFlashcardsFirst(),
        };
    }
}
