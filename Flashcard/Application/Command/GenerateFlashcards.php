<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;

class GenerateFlashcards
{
    public function __construct(
        private readonly Owner $owner,
        private readonly string $deck_name
    ) {}

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getDeckName(): string
    {
        return $this->deck_name;
    }
}
