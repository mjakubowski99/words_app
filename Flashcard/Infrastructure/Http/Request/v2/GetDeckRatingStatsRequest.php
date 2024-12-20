<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use Shared\Http\Request\Request;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class GetDeckRatingStatsRequest extends Request
{
    public function getDeckId(): FlashcardDeckId
    {
        return new FlashcardDeckId((int) $this->route('flashcard_deck_id'));
    }
}
