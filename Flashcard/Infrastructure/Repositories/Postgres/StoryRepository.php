<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\Story;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\StoryCollection;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Infrastructure\Mappers\Postgres\StoryMapper;

class StoryRepository implements IStoryRepository
{
    public function __construct(
        private StoryMapper $mapper,
    ) {}

    public function findRandomStoryIdByFlashcard(FlashcardId $id): ?StoryId
    {
        return $this->mapper->findRandomStoryIdByFlashcardId($id);
    }

    public function find(StoryId $id, UserId $user_id): ?Story
    {
        return $this->mapper->find($id, $user_id);
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
