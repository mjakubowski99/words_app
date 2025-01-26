<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Application\Repository\IFlashcardDuplicateRepository;

class FlashcardDuplicateService
{
    public function __construct(
        private IFlashcardDuplicateRepository $duplicate_repository,
    ) {}

    /** @param Flashcard[] $flashcards */
    public function removeDuplicates(Deck $deck, array $flashcards): array
    {
        $front_words = array_map(function (Flashcard $flashcard) {
            return $flashcard->getFrontWord();
        }, $flashcards);

        $duplicated_words = $this->duplicate_repository->getAlreadySavedFrontWords($deck->getId(), $front_words);

        $duplicated_words = array_map(fn ($word) => mb_strtolower($word), $duplicated_words);

        return array_filter($flashcards, function (Flashcard $flashcard) use ($duplicated_words) {
            return !in_array(mb_strtolower($flashcard->getFrontWord()), $duplicated_words, true);
        });
    }
}