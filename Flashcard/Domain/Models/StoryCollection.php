<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

class StoryCollection
{
    public function __construct(
        private array $stories,
        private array $pulled_flashcards = [],
    ) {}

    public function get(): array
    {
        return $this->stories;
    }

    public function unset(array $indexes): void
    {
        foreach ($indexes as $index) {
            unset($this->stories[$index]);
        }
    }

    public function getAllFlashcardsCount(): int
    {
        return count(iterator_to_array($this->getAllStoryFlashcards()));
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

    /** @param StoryFlashcard[] $stories_without_duplicates */
    public function pullStoriesWithDuplicates(array $stories_without_duplicates): array
    {
        $stories_to_remove = [];

        foreach ($this->get() as $index => $story) {
            $new_story_flashcards = [];

            foreach ($stories_without_duplicates as $story_flashcard) {
                if ($index === $story_flashcard->getIndex()) {
                    $new_story_flashcards[] = $story_flashcard;
                }
            }

            if (count($new_story_flashcards) !== count($story->getStoryFlashcards())) {
                $stories_to_remove[] = $index;
                $this->pulled_flashcards = array_merge(
                    $this->pulled_flashcards,
                    array_map(fn (StoryFlashcard $flashcard) => $flashcard->getFlashcard(), $new_story_flashcards)
                );
            } else {
                $story->setStoryFlashcards($new_story_flashcards);
            }
        }

        $this->unset($stories_to_remove);

        return $this->pulled_flashcards;
    }

    public function pullStoriesWithOnlyOneSentence(): void
    {
        $stories_to_remove = [];

        foreach ($this->get() as $index => $story) {
            if (count($story->getStoryFlashcards()) === 1) {
                $stories_to_remove[] = $index;
                $this->pulled_flashcards[] = $story->getStoryFlashcards()[0]->getFlashcard();
            }
        }

        $this->unset($stories_to_remove);
    }

    /** @return Flashcard[] */
    public function getPulledFlashcards(): array
    {
        return $this->pulled_flashcards;
    }
}
