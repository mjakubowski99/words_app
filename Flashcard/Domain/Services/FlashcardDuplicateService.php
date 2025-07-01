<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Application\Repository\IFlashcardDuplicateRepository;
use Flashcard\Domain\Models\StoryFlashcard;

class FlashcardDuplicateService
{
    public function __construct(
        private IFlashcardDuplicateRepository $duplicate_repository,
    ) {}

    /** @param StoryFlashcard[] $flashcards */
    public function removeDuplicates(Deck $deck, array $flashcards): array
    {
        $front_words = array_map(function (StoryFlashcard $flashcard) {
            return mb_strtolower($flashcard->getFlashcard()->getFrontWord());
        }, $flashcards);

        $unique_words = array_values(array_unique($front_words));

        $unique_flashcards = [];
        foreach ($unique_words as $unique_word) {
            foreach ($flashcards as $flashcard) {
                if (mb_strtolower($flashcard->getFlashcard()->getFrontWord()) === $unique_word) {
                    $unique_flashcards[] = $flashcard;

                    break;
                }
            }
        }

        $flashcards = $unique_flashcards;

        $duplicated_words = $this->duplicate_repository->getAlreadySavedFrontWords($deck->getId(), $front_words);

        $duplicated_words = array_map(fn ($word) => mb_strtolower($word), $duplicated_words);

        return array_filter($flashcards, function (StoryFlashcard $flashcard) use ($duplicated_words) {
            return !in_array(mb_strtolower($flashcard->getFlashcard()->getFrontWord()), $duplicated_words, true);
        });
    }
}
