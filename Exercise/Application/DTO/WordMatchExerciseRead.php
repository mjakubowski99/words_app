<?php

namespace Exercise\Application\DTO;

use Shared\Exercise\Exercises\IWordMatchExerciseRead;
use Shared\Utils\ValueObjects\ExerciseId;

class WordMatchExerciseRead implements IWordMatchExerciseRead
{
    public function __construct(
        private ExerciseId $exercise_id,
        private bool $is_story,
        private array $entries
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
}