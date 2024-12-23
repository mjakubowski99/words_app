<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

use Flashcard\Domain\Models\Rating;

class EverRatedNotVeryGoodFlashcardsFirst extends PostgresSortCriteria
{
    public function apply(): string
    {
        return 'sm_two_flashcards.min_rating < ' . Rating::VERY_GOOD->value . ' DESC';
    }
}
