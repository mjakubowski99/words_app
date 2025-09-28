<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Shared\Utils\ValueObjects\Language;
use Flashcard\Application\DTO\ResolvedDeck;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Flashcard\Application\Repository\IFlashcardDuplicateRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;

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

    public function generate(ResolvedDeck $deck, Language $front, Language $back, string $deck_name, int $words_count, int $words_count_to_save): int
    {
        $initial_letters_to_avoid = $this->duplicate_repository->getRandomFrontWordInitialLetters($deck->getDeck()->getId(), 5);

        $default_language_level = $deck->getDeck()->getDefaultLanguageLevel();

        $prompt = new FlashcardPrompt($deck_name, $default_language_level, $front, $back, $words_count, $initial_letters_to_avoid);

        try {
            $stories = $this->generator->generate($deck->getDeck()->getOwner(), $deck->getDeck(), $prompt);

            if ($words_count > $words_count_to_save) {
                $stories = $this->story_duplicate_service->removeDuplicates($deck, $stories, $words_count_to_save);

                $stories->pullStoriesWithOnlyOneSentence();

                if (count($stories->getPulledFlashcards()) > 0) {
                    $this->flashcard_repository->createMany($stories->getPulledFlashcards());
                }
            }

            if (count($stories->get()) > 0) {
                $this->repository->saveMany($stories);
            }

            return $stories->getAllFlashcardsCount();
        } catch (\Throwable $exception) {
            if (!$deck->isExistingDeck()) {
                $this->deck_repository->remove($deck->getDeck());
            }

            throw $exception;
        }
    }
}
