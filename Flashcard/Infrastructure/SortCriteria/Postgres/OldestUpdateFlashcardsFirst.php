<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

class OldestUpdateFlashcardsFirst extends PostgresSortCriteria
{
    public function apply(): string
    {
        return 'sm_two_flashcards.updated_at ASC NULLS FIRST';
    }
}
