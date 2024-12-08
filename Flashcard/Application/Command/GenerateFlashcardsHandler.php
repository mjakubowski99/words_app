<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Services\DeckResolver;
use Flashcard\Application\DTO\GenerateFlashcardsResult;
use Flashcard\Application\Services\FlashcardGeneratorService;

final readonly class GenerateFlashcardsHandler
{
    public function __construct(
        private DeckResolver $deck_resolver,
        private FlashcardGeneratorService $flashcard_generator_service,
    ) {}

    public function handle(GenerateFlashcards $command): GenerateFlashcardsResult
    {
        $resolved_deck = $this->deck_resolver->resolveByName(
            $command->getOwner(),
            $command->getDeckName(),
            $command->getLanguageLevel(),
        );

        $flashcards = $this->flashcard_generator_service->generate($resolved_deck, $command->getDeckName());

        return new GenerateFlashcardsResult(
            $resolved_deck->getDeck()->getId(),
            count($flashcards),
            $resolved_deck->isExistingDeck()
        );
    }
}
