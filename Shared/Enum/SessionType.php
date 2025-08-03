<?php

declare(strict_types=1);

namespace Shared\Enum;

enum SessionType: string
{
    case UNSCRAMBLE_WORDS = 'unscramble_words';
    case WORD_MATCH = 'word_match';
    case FLASHCARD = 'flashcard';
    case MIXED = 'mixed';

    public static function allowedInMixed(): array
    {
        return [
            self::FLASHCARD,
            self::UNSCRAMBLE_WORDS,
//            self::WORD_MATCH,
        ];
    }
}
