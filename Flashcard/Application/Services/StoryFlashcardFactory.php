<?php

namespace Flashcard\Application\Services;

use Flashcard\Application\DTO\SessionFlashcardSummaries;
use Flashcard\Application\DTO\SessionFlashcardSummary;
use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardExerciseCollection;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Domain\Models\StoryFlashcard;

class StoryFlashcardFactory
{
    public function __construct(
        private readonly IStoryRepository $repository,
        private readonly IFlashcardSelector $selector,
    ) {}

    public function make(NextSessionFlashcards $next_session_flashcards, Flashcard $base_flashcard): SessionFlashcardSummaries
    {
        $story = $this->repository->findRandomStoryByFlashcard($base_flashcard->getId());

        if ($story) {
            return SessionFlashcardSummaries::fromStory($story, $base_flashcard);
        }

        $flashcards = $this->selector->select(
            $next_session_flashcards,
            2,
            [$base_flashcard->getId()]
        );
        $flashcards = array_merge($flashcards, [$base_flashcard]);

        return SessionFlashcardSummaries::fromFlashcards($flashcards, $base_flashcard);
    }
}