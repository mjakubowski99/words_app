<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Flashcard;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class CreateFlashcardHandler
{
    public function __construct(
        private IFlashcardDeckRepository $deck_repository,
        private IFlashcardRepository $repository
    ) {}

    public function handle(CreateFlashcard $command): void
    {
        $deck = $this->deck_repository->findById($command->getDeckId());

        if (!$deck->getOwner()->equals($command->getOwner())) {
            throw new ForbiddenException('You must be deck owner to create flashcard');
        }

        $flashcard = new Flashcard(
            FlashcardId::noId(),
            $command->getFrontWord(),
            $command->getFrontLang(),
            $command->getBackWord(),
            $command->getBackLang(),
            $command->getFrontContext(),
            $command->getBackContext(),
            $command->getOwner(),
            $deck,
        );

        $this->repository->createMany([$flashcard]);
    }
}
