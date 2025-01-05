<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

class OlderThanThirtySecondsAgo extends PostgresSortCriteria
{
    public function apply(): string
    {
        return "
             CASE 
                 WHEN sm_two_flashcards.updated_at < NOW() - INTERVAL '30 seconds' THEN 0
             ELSE 1 
            END ASC
        ";
    }
}