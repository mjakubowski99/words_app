<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\ExerciseFlashcardFactory;

use Flashcard\Application\DTO\SessionFlashcardSummaries;
use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Domain\Models\StoryFlashcard;
use Shared\Utils\Cache\ICache;
use Shared\Utils\ValueObjects\UserId;

class WordMatchExerciseFlashcardFactory implements IExerciseFlashcardFactory
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

        $story_id = $this->repository->findRandomStoryIdByFlashcard($base_flashcard->getId());

        if ($story_id && !in_array($story_id->getValue(), $latest_stories, true)) {
            $latest_stories[] = $story_id->getValue();

            $this->cache->put($key, json_encode($latest_stories), 5 * 60);

            $story = $this->repository->find($story_id, $next_session_flashcards->getUserId());

            $exclude_ids = array_map(
                fn(StoryFlashcard $flashcard) => $flashcard->getFlashcard()->getId(),
                $story->getStoryFlashcards()
            );

            $flashcards = $this->selector->select(
                $next_session_flashcards,
                3,
                array_merge([$base_flashcard->getId()], $exclude_ids)
            );

            return SessionFlashcardSummaries::fromStory(
                $this->repository->find($story_id, $next_session_flashcards->getUserId()),
                $base_flashcard,
                $flashcards
            );
        }

        $flashcards = $this->selector->select(
            $next_session_flashcards,
            self::FLASHCARDS_COUNT_TO_ADD + 3,
            [$base_flashcard->getId()]
        );

        $flashcards = array_merge([$base_flashcard], $flashcards);

        return SessionFlashcardSummaries::fromFlashcards(
            array_slice($flashcards, 0, self::FLASHCARDS_COUNT_TO_ADD),
            $base_flashcard,
            array_slice($flashcards, self::FLASHCARDS_COUNT_TO_ADD),
        );
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
