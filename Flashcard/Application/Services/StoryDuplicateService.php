<?php

namespace Flashcard\Application\Services;

use Flashcard\Application\DTO\ResolvedDeck;
use Flashcard\Domain\Models\StoryCollection;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\Services\FlashcardDuplicateService;

class StoryDuplicateService
{
    public function __construct(
        private FlashcardDuplicateService $duplicate_service,
    ) {}
    public function removeDuplicates(ResolvedDeck $deck, StoryCollection $stories, int $words_count_to_save): array
    {
        $story_flashcards = $this->duplicate_service->removeDuplicates($deck->getDeck(), $stories);

        $story_flashcards = array_slice($story_flashcards, 0, $words_count_to_save);

        $flashcards_not_in_story = [];

        $stories_to_remove = [];

        foreach ($stories->get() as $index => $story) {
            $new_story_flashcards = [];

            foreach ($story_flashcards as $story_flashcard) {
                if ($index === $story_flashcard->getIndex()) {
                    $new_story_flashcards[] = $story_flashcard;
                }
            }

            if (count($new_story_flashcards) !== count($story->getStoryFlashcards())) {
                $stories_to_remove[] = $index;
                $flashcards_not_in_story = array_merge(
                    $flashcards_not_in_story,
                    array_map(fn(StoryFlashcard $flashcard) => $flashcard->getFlashcard(), $new_story_flashcards)
                );
            } else {
                $story->setStoryFlashcards($new_story_flashcards);
            }
        }

        $stories->unset($stories_to_remove);

        return $flashcards_not_in_story;
    }
}