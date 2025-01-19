<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\Repository\IFlashcardRepository;

class BulkDeleteFlashcardHandler
{
    public function __construct(
        private IFlashcardRepository $repository
    ) {}

    /** @param FlashcardId[] $flashcard_ids */
    public function handle(UserId $user_id, array $flashcard_ids): void
    {
        $this->repository->bulkDelete($user_id, $flashcard_ids);
    }
}
