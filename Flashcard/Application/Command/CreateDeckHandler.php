<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class CreateDeckHandler
{
    public function __construct(
        private IFlashcardDeckRepository $repository,
    ) {}

    public function handle(CreateDeckCommand $command): FlashcardDeckId
    {
        $deck = new Deck(
            $command->getOwner(),
            $command->getTag(),
            $command->getName(),
            $command->getDefaultLanguageLevel()
        );

        $deck = $this->repository->create($deck);

        return $deck->getId();
    }
}
