<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Story;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\StoryCollection;
use Flashcard\Domain\ValueObjects\FlashcardId;

interface IStoryRepository
{
    public function findRandomStoryIdByFlashcard(FlashcardId $id): ?StoryId;
    public function find(StoryId $id, UserId $user_id): ?Story;

    public function saveMany(StoryCollection $stories): void;

    public function bulkDelete(array $story_ids): void;
}
