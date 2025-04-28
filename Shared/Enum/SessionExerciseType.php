<?php

namespace Shared\Enum;

enum SessionExerciseType: string
{
    case UNSCRAMBLE_WORDS = 'unscramble_words';
    case FLASHCARD = 'flashcard';
    case MIXED = 'mixed';

    public static function allowedInMixed(): array
    {
        return [
            self::FLASHCARD,
            self::UNSCRAMBLE_WORDS,
        ];
    }
}