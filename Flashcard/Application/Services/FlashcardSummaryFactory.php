<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Application\Services\ExerciseFlashcardFactory\IExerciseFlashcardFactory;
use Flashcard\Application\Services\ExerciseFlashcardFactory\UnscrambleWordExerciseFlashcardFactory;
use Flashcard\Application\Services\ExerciseFlashcardFactory\WordMatchExerciseFlashcardFactory;
use Shared\Enum\ExerciseType;

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
        throw new \InvalidArgumentException('Unsupported exercise type: ' . $type->value);
    }
}
