<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\Deck;

class ResolvedDeck
{
    public function __construct(
        private bool $is_existing_deck,
        private Deck $deck
    ) {}

    public function isExistingDeck(): bool
    {
        return $this->is_existing_deck;
    }

    public function getDeck(): Deck
    {
        return $this->deck;
    }
}
