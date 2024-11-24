<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

class NotHardFlashcardsFirst extends PostgresSortCriteria
{
    public function apply(): string
    {
        return 'CASE WHEN COALESCE(sm_two_flashcards.repetition_interval, 1.0) > 1.0 THEN 1 ELSE 0 END ASC';
    }
}
