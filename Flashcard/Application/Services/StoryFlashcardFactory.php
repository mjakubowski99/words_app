<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Application\DTO\SessionFlashcardSummaries;
use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Shared\Utils\Cache\ICache;
use Shared\Utils\ValueObjects\UserId;

class StoryFlashcardFactory
{
    private const int FLASHCARDS_COUNT_TO_ADD = 2;

    public function __construct(
        private readonly IStoryRepository $repository,
        private readonly IFlashcardSelector $selector,
        private readonly ICache $cache,
    ) {}

    public function make(NextSessionFlashcards $next_session_flashcards, Flashcard $base_flashcard): SessionFlashcardSummaries
    {
        $key = $this->getCache($next_session_flashcards->getUserId());

        $latest_stories = $this->retrieveStoriesFromCache($key);

        $story = $this->repository->findRandomStoryByFlashcard($base_flashcard->getId(), $next_session_flashcards->getUserId());

        if ($story && !in_array($story->getId()->getValue(), $latest_stories, true)) {
            $latest_stories[] = $story->getId()->getValue();

            $this->cache->put($key, json_encode($latest_stories), 5 * 60);

            return SessionFlashcardSummaries::fromStory($story, $base_flashcard);
        }

        $flashcards = $this->selector->select(
            $next_session_flashcards,
            self::FLASHCARDS_COUNT_TO_ADD,
            [$base_flashcard->getId()]
        );
        $flashcards = array_merge($flashcards, [$base_flashcard]);

        return SessionFlashcardSummaries::fromFlashcards($flashcards, $base_flashcard);
    }

    private function getCache(UserId $user_id): string
    {
        return 'latest-stories:' . $user_id->getValue();
    }

    private function retrieveStoriesFromCache(string $key): array
    {
        $latest_stories = $this->cache->get($key);

        return $latest_stories ? json_decode($latest_stories, true) : [];
    }
}