<?php

namespace Flashcard\Application\Services;

use Flashcard\Application\DTO\ResolvedDeck;
use Flashcard\Domain\Models\StoryCollection;
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

        return $stories->pullStoriesWithDuplicates($story_flashcards);
    }
}