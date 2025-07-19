<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Application\DTO\ResolvedDeck;
use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Flashcard\Application\Repository\IFlashcardDuplicateRepository;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;
use Flashcard\Domain\Models\FlashcardPrompt;

class FlashcardGeneratorService
{
    public function __construct(
        private IFlashcardDeckRepository $deck_repository,
        private IFlashcardRepository $flashcard_repository,
        private IStoryRepository $repository,
        private IFlashcardGenerator $generator,
        private IFlashcardDuplicateRepository $duplicate_repository,
        private StoryDuplicateService $story_duplicate_service,
    ) {}

    public function generate(
        ResolvedDeck $deck,
        string $deck_name,
        int $words_count,
        int $words_count_to_save,
    ): int {
        $initial_letters_to_avoid = $this->duplicate_repository->getRandomFrontWordInitialLetters($deck->getDeck()->getId(), 5);

        $prompt = new FlashcardPrompt(
            $deck_name,
            $deck->getDeck()->getDefaultLanguageLevel(),
            $words_count,
            $initial_letters_to_avoid
        );

        try {
            $stories = $this->generator->generate($deck->getDeck()->getOwner(), $deck->getDeck(), $prompt);

            if ($words_count > $words_count_to_save) {
                $flashcards_not_in_story = $this->story_duplicate_service->removeDuplicates($deck, $stories, $words_count_to_save);

                if (count($flashcards_not_in_story) > 0) {
                    $this->flashcard_repository->createMany($flashcards_not_in_story);
                }
            }

            if (count($stories->get()) > 0) {
                $this->repository->saveMany($stories);
            }

            $count = 0;
            foreach ($stories->get() as $story) {
                $count += count($story->getStoryFlashcards());
            }

            return $count;
        } catch (\Throwable $exception) {
            if (!$deck->isExistingDeck()) {
                $this->deck_repository->remove($deck->getDeck());
            }

            throw $exception;
        }
    }
}
