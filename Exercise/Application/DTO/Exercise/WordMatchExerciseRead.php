<?php

declare(strict_types=1);

namespace Exercise\Application\DTO\Exercise;

use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Exercise\Exercises\IWordMatchExerciseRead;

class WordMatchExerciseRead implements IWordMatchExerciseRead
{
    public function __construct(
        private ExerciseId $exercise_id,
        private bool $is_story,
        private array $entries,
        private array $options = []
    ) {}

    public function getExerciseId(): ExerciseId
    {
        return $this->exercise_id;
    }

    public function isStory(): bool
    {
        return $this->is_story;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getAnswerOptions(): array
    {
        return $this->options;
    }
}
