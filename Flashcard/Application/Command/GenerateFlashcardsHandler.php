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

    public function handle(GenerateFlashcards $command, int $flashcards_limit, int $flashcards_save_limit): GenerateFlashcardsResult
    {
        $resolved_deck = $this->deck_resolver->resolveByName(
            $command->getUserId(),
            $command->getDeckName(),
            $command->getLanguageLevel(),
        );

        $flashcards_count = $this->flashcard_generator_service->generate(
            $resolved_deck,
            $command->getFront(),
            $command->getBack(),
            $command->getDeckName(),
            $flashcards_limit,
            $flashcards_save_limit
        );

        return new GenerateFlashcardsResult(
            $resolved_deck->getDeck()->getId(),
            $flashcards_count,
            $resolved_deck->isExistingDeck()
        );
    }
}
