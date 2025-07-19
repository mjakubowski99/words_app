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

    /**
     * @return \Generator<StoryFlashcard>
     */
    public function getAllStoryFlashcards(): \Generator
    {
        foreach ($this->stories as $index => $story) {
            foreach ($story->getStoryFlashcards() as $story_flashcard) {
                yield $story_flashcard;
            }
        }
    }
}