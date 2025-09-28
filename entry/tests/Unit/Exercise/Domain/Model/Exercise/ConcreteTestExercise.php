<?php

declare(strict_types=1);

namespace Tests\Unit\Exercise\Domain\Model\Exercise;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\Models\Exercise\Exercise;

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
