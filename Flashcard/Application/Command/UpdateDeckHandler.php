<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class UpdateDeckHandler
{
    public function __construct(
        private IFlashcardDeckRepository $repository,
    ) {}

    public function handle(Owner $owner, FlashcardDeckId $id, string $name): void
    {
        $deck = $this->repository->findById($id);

        if (!$deck->getOwner()->equals($owner)) {
            throw new ForbiddenException();
        }

        $deck->setName($name);

        $this->repository->update($deck);
    }
}
