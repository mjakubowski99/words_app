<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class BulkDeleteDeckHandler
{
    public function __construct(
        private IFlashcardDeckRepository $repository
    ) {}

    /** @param FlashcardDeckId[] $flashcard_deck_ids */
    public function handle(UserId $user_id, array $flashcard_deck_ids): void
    {
        $this->repository->bulkDelete($user_id, $flashcard_deck_ids);
    }
}
