<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\Repository\IFlashcardRepository;

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
