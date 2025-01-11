<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Models\Emoji;
use Flashcard\Domain\Models\Flashcard;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Application\Repository\IFlashcardRepository;

class UpdateFlashcardHandler
{
    public function __construct(
        private IFlashcardRepository $repository
    ) {}

    public function handle(UpdateFlashcard $command): void
    {
        $flashcard = $this->repository->find($command->getId());

        if (!$flashcard->getOwner()->equals($command->getOwner())) {
            throw new ForbiddenException('You must be flashcard owner to update flashcard');
        }

        $flashcard = new Flashcard(
            $command->getId(),
            $command->getFrontWord(),
            $command->getFrontLang(),
            $command->getBackWord(),
            $command->getBackLang(),
            $command->getFrontContext(),
            $command->getBackContext(),
            $command->getOwner(),
            $flashcard->getDeck(),
            $command->getLanguageLevel(),
            $command->getEmoji() ? new Emoji($command->getEmoji()) : null,
        );

        $this->repository->update($flashcard);
    }
}
