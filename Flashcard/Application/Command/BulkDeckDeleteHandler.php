<?php

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Shared\Utils\ValueObjects\UserId;

class BulkDeckDeleteHandler
{
    public function __construct(
        private IFlashcardDeckRepository $repository
    ) {}

    public function handle(UserId $user_id, array $deck_ids): void
    {
        $this->repository->bulkDelete($user_id, $deck_ids);
    }
}