<?php

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Story;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Utils\ValueObjects\UserId;

interface IStoryRepository
{
    public function findRandomStoryByFlashcard(FlashcardId $id, UserId $user_id): ?Story;
    /** @param Story[] $stories*/
    public function saveMany(array $stories): void;
}