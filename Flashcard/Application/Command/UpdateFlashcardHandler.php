<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

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
            $command->getWord(),
            $command->getWordLang(),
            $command->getTranslation(),
            $command->getTranslationLang(),
            $command->getContext(),
            $command->getContextTranslation(),
            $command->getOwner(),
            $flashcard->getCategory()
        );

        $this->repository->update($flashcard);
    }
}
