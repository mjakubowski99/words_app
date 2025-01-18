<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class BulkDeleteDeckHandler
{
    public function __construct(
        private IFlashcardDeckRepository $repository
    ) {}

    public function handle(UserId $user_id, array $deck_ids): void
    {
        $this->repository->bulkDelete($user_id, $deck_ids);
    }
}
