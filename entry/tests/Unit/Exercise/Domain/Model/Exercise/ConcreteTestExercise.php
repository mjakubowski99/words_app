<?php

declare(strict_types=1);

namespace Tests\Unit\Exercise\Domain\Model\Exercise;

use Exercise\Domain\Models\Exercise;
use Exercise\Domain\Models\ExerciseStatus;
use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\UserId;

class ConcreteTestExercise extends Exercise
{
    public static function new(ExerciseStatus $status): self
    {
        return new self(
            new ExerciseId(1),
            UserId::new(),
            [],
            $status,
            ExerciseType::UNSCRAMBLE_WORDS
        );
    }
}
