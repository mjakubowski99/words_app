<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Application\Repository\IFlashcardRepository;

class BulkDeleteFlashcardHandler
{
    public function __construct(
        private IFlashcardRepository $repository,
        private IStoryRepository $story_repository,
    ) {}

    /** @param FlashcardId[] $flashcard_ids */
    public function handle(UserId $user_id, array $flashcard_ids): void
    {
        $story_ids = $this->repository->getStoryIdForFlashcards($flashcard_ids);

        if (count($story_ids) > 0) {
            $this->story_repository->bulkDelete($story_ids);
        }

        $this->repository->bulkDelete($user_id, $flashcard_ids);
    }
}
