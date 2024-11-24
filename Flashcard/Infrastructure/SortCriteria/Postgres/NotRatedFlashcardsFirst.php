<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

class NotRatedFlashcardsFirst extends PostgresSortCriteria
{
    public function apply(): string
    {
        return '
            CASE 
                WHEN sm_two_flashcards.repetition_interval IS NULL THEN 1
                ELSE 0
            END DESC
        ';
    }
}
