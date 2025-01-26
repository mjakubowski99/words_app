<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Application\DTO\ResolvedDeck;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\Services\FlashcardDuplicateService;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Flashcard\Application\Repository\IFlashcardDuplicateRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;

class FlashcardGeneratorService
{
    public function __construct(
        private IFlashcardDeckRepository $deck_repository,
        private IFlashcardRepository $repository,
        private IFlashcardGenerator $generator,
        private IFlashcardDuplicateRepository $duplicate_repository,
        private FlashcardDuplicateService $duplicate_service,
    ) {}

    public function generate(
        ResolvedDeck $deck,
        string $deck_name,
        int $words_count,
        int $words_count_to_save,
    ): array {
        $initial_letters_to_avoid = $this->duplicate_repository->getRandomFrontWordInitialLetters($deck->getDeck()->getId(), 5);

        $prompt = new FlashcardPrompt(
            $deck_name,
            $deck->getDeck()->getDefaultLanguageLevel(),
            $words_count,
            $initial_letters_to_avoid
        );

        try {
            $flashcards = $this->generator->generate($deck->getDeck()->getOwner(), $deck->getDeck(), $prompt);

            if ($words_count > $words_count_to_save) {
                $flashcards = $this->duplicate_service->removeDuplicates($deck->getDeck(), $flashcards);
            }

            if (count($flashcards) > $words_count_to_save) {
                $flashcards = array_slice($flashcards, 0, $words_count_to_save);
            }

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
