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
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\Services\FlashcardDuplicateService;

class FlashcardGeneratorService
{
    public function __construct(
        private IFlashcardDeckRepository $deck_repository,
        private IFlashcardRepository $flashcard_repository,
        private IStoryRepository $repository,
        private IFlashcardGenerator $generator,
        private IFlashcardDuplicateRepository $duplicate_repository,
        private FlashcardDuplicateService $duplicate_service,
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
                $story_flashcards = $this->duplicate_service->removeDuplicates($deck->getDeck(), $stories->getAllStoryFlashcards());

                $story_flashcards = array_slice($story_flashcards, 0, $words_count_to_save);

                $flashcards_not_in_story = [];
                $stories_to_remove = [];

                foreach ($stories->get() as $index => $story) {
                    $new_story_flashcards = [];

                    foreach ($story_flashcards as $story_flashcard) {
                        if ($index === $story_flashcard->getIndex()) {
                            $new_story_flashcards[] = $story_flashcard;
                        }
                    }

                    if (count($new_story_flashcards) !== count($story->getStoryFlashcards())) {
                        $stories_to_remove[] = $index;
                        $flashcards_not_in_story = array_merge(
                            $flashcards_not_in_story,
                            array_map(fn(StoryFlashcard $flashcard) => $flashcard->getFlashcard(), $new_story_flashcards)
                        );
                    } else {
                        $story->setStoryFlashcards($new_story_flashcards);
                    }
                }

                $stories->unset($stories_to_remove);

                if (count($flashcards_not_in_story) > 0) {
                    $this->flashcard_repository->createMany($flashcards_not_in_story);
                }
            }

            $this->repository->saveMany($stories);

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
