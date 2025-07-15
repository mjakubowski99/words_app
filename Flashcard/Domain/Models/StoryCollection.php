<?php

namespace Flashcard\Domain\Models;

class StoryCollection
{
    public function __construct(
        private array $stories,
    ) {}

    public function get(): array
    {
        return array_values($this->stories);
    }

    public function unset(array $indexes): void
    {
        foreach ($indexes as $index) {
            unset($this->stories[$index]);
        }
    }

    public function getAllStoryFlashcards()
    {
        $story_flashcards = [];

        foreach ($this->stories as $index => $story) {
            $story_flashcards = array_merge($story_flashcards, $story->getStoryFlashcards());
        }

        return $story_flashcards;
    }
}