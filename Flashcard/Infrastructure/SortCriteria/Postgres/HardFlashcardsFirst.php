<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

class HardFlashcardsFirst extends PostgresSortCriteria
{
    public function apply(): string
    {
        return 'COALESCE(sm_two_flashcards.repetition_interval, 1.0) ASC';
    }
}
