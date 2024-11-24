<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

class RandomizeLatestFlashcardOrder extends PostgresSortCriteria
{
    public function apply(): string
    {
        $random = round(lcg_value(), 2);

        return "
            CASE 
                WHEN sm_two_flashcards.repetition_interval IS NOT NULL AND {$random} < 0.7 THEN 1
            ELSE 0
            END DESC
        ";
    }
}
