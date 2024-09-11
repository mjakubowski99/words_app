<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\Session;

interface IFlashcardSelector
{
    /** @return Flashcard[] */
    public function select(Session $session, int $limit): array;
}
