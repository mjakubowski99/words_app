<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class UpdateDeckActivityCommand
{
    public function __construct(
        private IFlashcardDeckRepository $repository
    ) {}

    public function updateLastViewed(FlashcardDeckId $id, UserId $user_id): void
    {
        $this->repository->updateLastViewedAt($id, $user_id);
    }
}
