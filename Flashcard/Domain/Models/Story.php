<?php

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\StoryId;

class Story
{
    public function __construct(
        private StoryId $id,
        private array $flashcards,
    ) {}

    public function getId(): StoryId
    {
        return $this->id;
    }

    /** @return StoryFlashcard[] */
    public function getStoryFlashcards(): array
    {
        return $this->flashcards;
    }

    public function setStoryFlashcards(array $flashcards): void
    {
        $this->flashcards = $flashcards;
    }
}