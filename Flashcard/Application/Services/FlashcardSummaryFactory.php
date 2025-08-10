<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Shared\Enum\ExerciseType;
use Flashcard\Application\Services\ExerciseFlashcardFactory\IExerciseFlashcardFactory;
use Flashcard\Application\Services\ExerciseFlashcardFactory\WordMatchExerciseFlashcardFactory;
use Flashcard\Application\Services\ExerciseFlashcardFactory\UnscrambleWordExerciseFlashcardFactory;

class FlashcardSummaryFactory
{
    public function __construct(
        private UnscrambleWordExerciseFlashcardFactory $unscramble_factory,
        private WordMatchExerciseFlashcardFactory $word_match_factory,
    ) {}

    public function make(ExerciseType $type): IExerciseFlashcardFactory
    {
        switch ($type) {
            case ExerciseType::UNSCRAMBLE_WORDS:
                return $this->unscramble_factory;

            case ExerciseType::WORD_MATCH:
                return $this->word_match_factory;
        }

        /* @phpstan-ignore-next-line */
        throw new \InvalidArgumentException('Unsupported exercise type: ' . $type->value);
    }
}
