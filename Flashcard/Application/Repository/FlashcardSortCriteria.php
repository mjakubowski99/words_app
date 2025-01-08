<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

enum FlashcardSortCriteria: string
{
    case NOT_RATED_FLASHCARDS_FIRST = 'NOT_RATED_FLASHCARDS_FIRST';
    case HARD_FLASHCARDS_FIRST = 'HARD_FLASHCARDS_FIRST';
    case LOWEST_REPETITION_INTERVAL_FIRST = 'LOWEST_REPETITION_INTERVAL_FIRST';
    case PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST = 'PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST';
    case OLDEST_UPDATE_FLASHCARDS_FIRST = 'OLDEST_UPDATE_FLASHCARDS_FIRST';
    case NOT_HARD_FLASHCARDS_FIRST = 'NOT_HARD_FLASHCARDS_FIRST';
    case RANDOMIZE_LATEST_FLASHCARDS_ORDER = 'RANDOMIZE_LATEST_FLASHCARDS_ORDER';
    case OLDER_THAN_FIVE_MINUTES_AGO_FIRST = 'OLDER_THAN_FIVE_MINUTES_AGO_FIRST';
    case OLDER_THAN_FIFTEEN_SECONDS_AGO = 'OLDER_THAN_THIRTY_SECONDS_AGO_FIRST';
    case EVER_NOT_VERY_GOOD_FIRST = 'EVER_NOT_VERY_GOOD_FIRST';
}
