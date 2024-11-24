<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

enum FlashcardSortCriteria: string
{
    case NOT_RATED_FLASHCARDS_FIRST = 'not_rated_flashcards_first';
    case HARD_FLASHCARDS_FIRST = 'hard_flashcards_first';
    case LOWEST_REPETITION_INTERVAL_FIRST = 'by_repetition_interval';
    case PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST = 'by_planned_flashcards_for_current_date';
    case OLDEST_UPDATE_FLASHCARDS_FIRST = 'by_oldest_update';
    case NOT_HARD_FLASHCARDS_FIRST = 'repetition_interval_greater_than_one_first';
    case RANDOMIZE_LATEST_FLASHCARDS_ORDER = 'randomize_latest_flashcards_order';
}
