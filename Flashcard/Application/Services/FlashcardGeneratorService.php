<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Application\DTO\ResolvedDeck;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;

class FlashcardGeneratorService
{
    public function __construct(
        private IFlashcardDeckRepository $deck_repository,
        private IFlashcardRepository $repository,
        private IFlashcardGenerator $generator,
    ) {}

    public function generate(ResolvedDeck $deck, string $deck_name): array
    {
        $prompt = new FlashcardPrompt($deck_name, $deck->getDeck()->getDefaultLanguageLevel());

        try {
            $flashcards = $this->generator->generate($deck->getDeck()->getOwner(), $deck->getDeck(), $prompt);

            $this->repository->createMany($flashcards);

            return $flashcards;
        } catch (\Throwable $exception) {
            if (!$deck->isExistingDeck()) {
                $this->deck_repository->remove($deck->getDeck());
            }

            throw $exception;
        }
    }
}
