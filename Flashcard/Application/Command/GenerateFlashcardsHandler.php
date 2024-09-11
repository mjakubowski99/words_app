<?php

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Services\FlashcardGenerator;

class GenerateFlashcardsHandler
{
    public function __construct(
        private readonly FlashcardGenerator $generator
    ) {}

    public function handle(GenerateFlashcards $command): CategoryId
    {
        return $this->generator->withAI($command->getOwner(), $command->getCategoryName())->getId();
    }
}