<?php

namespace Flashcard\Application\Services;

use Flashcard\Application\DTO\SessionFlashcardSummaries;
use Flashcard\Application\DTO\SessionFlashcardSummary;
use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardExerciseCollection;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Domain\Models\StoryFlashcard;
use Illuminate\Support\Facades\Cache;

class StoryFlashcardFactory
{
    public function __construct(
        private readonly IStoryRepository $repository,
        private readonly IFlashcardSelector $selector,
    ) {}

    public function make(NextSessionFlashcards $next_session_flashcards, Flashcard $base_flashcard): SessionFlashcardSummaries
    {
        $key = 'latest-stories:' . $next_session_flashcards->getUserId()->getValue();

        $latest_stories = Cache::get($key);

        if (!$latest_stories) {
            $latest_stories = [];
        } else {
            $latest_stories = json_decode($latest_stories, true);
        }

        $story = $this->repository->findRandomStoryByFlashcard($base_flashcard->getId(), $next_session_flashcards->getUserId());

        if ($story && !in_array($story->getId()->getValue(), $latest_stories, true)) {
            $latest_stories[] = $story->getId()->getValue();
            Cache::put($key, json_encode($latest_stories), 5*60);

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