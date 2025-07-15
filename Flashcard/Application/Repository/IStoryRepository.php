<?php

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Story;
use Flashcard\Domain\Models\StoryCollection;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Utils\ValueObjects\UserId;

interface IStoryRepository
{
    public function findRandomStoryByFlashcard(FlashcardId $id, UserId $user_id): ?Story;
    public function saveMany(StoryCollection $stories): void;
    public function bulkDelete(array $story_ids): void;
}