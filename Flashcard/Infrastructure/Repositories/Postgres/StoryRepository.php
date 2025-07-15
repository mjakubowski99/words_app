<?php

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Domain\Models\Story;
use Flashcard\Domain\Models\StoryCollection;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Infrastructure\Mappers\Postgres\StoryMapper;
use Shared\Utils\ValueObjects\UserId;

class StoryRepository implements IStoryRepository
{
    public function __construct(
        private StoryMapper $mapper,
    ) {}

    public function findRandomStoryByFlashcard(FlashcardId $id, UserId $user_id): ?Story
    {
        return $this->mapper->findRandomStoryByFlashcardId($id, $user_id);
    }

    public function saveMany(StoryCollection $stories): void
    {
        $this->mapper->saveMany($stories);
    }

    public function bulkDelete(array $story_ids): void
    {
        $this->mapper->bulkDelete($story_ids);
    }
}