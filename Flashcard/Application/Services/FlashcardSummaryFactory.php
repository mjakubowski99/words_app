<?php

namespace Flashcard\Application\Services;

use Flashcard\Application\DTO\SessionFlashcardSummaries;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Shared\Enum\ExerciseType;

class FlashcardSummaryFactory
{
    public function __construct(
        private StoryFlashcardFactory $factory,
    ) {}

    public function make(NextSessionFlashcards $flashcards, ExerciseType $type, Flashcard $base_flashcard): SessionFlashcardSummaries
    {
        switch ($type) {
            case ExerciseType::UNSCRAMBLE_WORDS:
                return SessionFlashcardSummaries::fromFlashcards([$base_flashcard], $base_flashcard);
            case ExerciseType::WORD_MATCH:
                return $this->factory->make($flashcards, $base_flashcard);
        }
    }
}