<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class OwnerCategoryRead
{
    public function __construct(private FlashcardDeckId $id, private string $name) {}

    public function getId(): FlashcardDeckId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
