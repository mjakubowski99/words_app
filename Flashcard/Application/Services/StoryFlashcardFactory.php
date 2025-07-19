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
use Shared\Utils\Cache\ICache;
use Shared\Utils\ValueObjects\UserId;

class StoryFlashcardFactory
{
    public function __construct(
        private readonly IStoryRepository $repository,
        private readonly IFlashcardSelector $selector,
        private ICache $cache,
    ) {}

    public function make(NextSessionFlashcards $next_session_flashcards, Flashcard $base_flashcard): SessionFlashcardSummaries
    {
        $key = $this->getCache($next_session_flashcards->getUserId());

        $latest_stories = $this->cache->get($key);

        if (!$latest_stories) {
            $latest_stories = [];
        } else {
            $latest_stories = json_decode($latest_stories, true);
        }

        $story = $this->repository->findRandomStoryByFlashcard($base_flashcard->getId(), $next_session_flashcards->getUserId());

        if ($story && !in_array($story->getId()->getValue(), $latest_stories, true)) {
            $latest_stories[] = $story->getId()->getValue();

            $this->cache->put($key, json_encode($latest_stories), 5 * 60);

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

    private function getCache(UserId $user_id): string
    {
        return 'latest-stories:' . $user_id->getValue();
    }
}