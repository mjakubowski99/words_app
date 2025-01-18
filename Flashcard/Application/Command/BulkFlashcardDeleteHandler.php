<?php

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\IFlashcardRepository;
use Shared\Utils\ValueObjects\UserId;

class BulkFlashcardDeleteHandler
{
    public function __construct(
        private IFlashcardRepository $repository
    ) {}

    public function handle(UserId $user_id, array $flashcard_ids): void
    {
        $this->repository->bulkDelete($user_id, $flashcard_ids);
    }
}