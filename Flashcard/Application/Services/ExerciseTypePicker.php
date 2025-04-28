<?php

namespace Flashcard\Application\Services;

use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Enum\ExerciseType;
use Shared\Enum\SessionExerciseType;

class ExerciseTypePicker
{
    public function pick(SessionExerciseType $type, SessionId $id): ?ExerciseType
    {
        if ($type === SessionExerciseType::MIXED) {
            $type = SessionExerciseType::allowedInMixed()[array_rand(SessionExerciseType::allowedInMixed())];
        }

        if ($type === SessionExerciseType::UNSCRAMBLE_WORDS) {
            return ExerciseType::UNSCRAMBLE_WORDS;
        } else if ($type === SessionExerciseType::FLASHCARD) {
            return null;
        }
    }
}