<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

class PlannedFlashcardsForCurrentDateFirst extends PostgresSortCriteria
{
    public function apply(): string
    {
        return '
            CASE 
                WHEN sm_two_flashcards.updated_at IS NOT NULL AND sm_two_flashcards.repetition_interval IS NOT NULL 
                     AND DATE(sm_two_flashcards.updated_at) + CAST(sm_two_flashcards.repetition_interval AS INTEGER) <= CURRENT_DATE
                THEN 1
                ELSE 0
            END DESC
        ';
    }
}
