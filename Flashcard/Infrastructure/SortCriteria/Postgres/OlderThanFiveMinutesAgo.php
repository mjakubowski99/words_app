<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

class OlderThanFiveMinutesAgo extends PostgresSortCriteria
{
    public function apply(): string
    {
        return "
             CASE 
                 WHEN sm_two_flashcards.updated_at < NOW() - INTERVAL '5 minutes' THEN 0
             ELSE 1 
            END ASC
        ";
    }
}
