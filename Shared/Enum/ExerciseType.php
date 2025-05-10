<?php

declare(strict_types=1);

namespace Shared\Enum;

enum ExerciseType: string
{
    case UNSCRAMBLE_WORDS = 'unscramble_words';

    public const array NUMBER_REPRESENTATIONS = [
        0 => self::UNSCRAMBLE_WORDS,
    ];

    public function toNumber(): int
    {
        foreach (self::NUMBER_REPRESENTATIONS as $key => $value) {
            /* @phpstan-ignore-next-line */
            if ($value->value === $this->value) {
                return $key;
            }
        }

        /* @phpstan-ignore-next-line */
        throw new \InvalidArgumentException('Invalid exercise type');
    }

    public static function fromNumber(int $number): self
    {
        return self::NUMBER_REPRESENTATIONS[$number]
            ?? throw new \InvalidArgumentException('Invalid exercise type number');
    }
}
