<?php

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Domain\Models\Story;
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

    /** @param Story[] $stories*/
    public function saveMany(array $stories): void
    {
        $this->mapper->saveMany($stories);
    }
}