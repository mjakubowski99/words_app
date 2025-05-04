<?php

declare(strict_types=1);

namespace Shared\Flashcard;

use Flashcard\Domain\Models\Rating;

interface ISessionFlashcardRating
{
    public function getSessionFlashcardId(): int;

    public function getRating(): Rating;
}
