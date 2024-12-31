<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\UserId;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\Repository\IFlashcardRepository;

class DeleteFlashcardHandler
{
    public function __construct(
        private IFlashcardRepository $repository
    ) {}

    public function handle(UserId $user_id, FlashcardId $id): void
    {
        $flashcard = $this->repository->find($id);

        if (!$flashcard->getOwner()->equals(Owner::fromUser($user_id))) {
            throw new ForbiddenException('You must be flashcard owner to delete a flashcard');
        }

        $this->repository->delete($id);
    }
}
