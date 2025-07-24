<?php

declare(strict_types=1);

namespace Exercise\Application\ReadModels;

class WordMatchExerciseRead
{
    public function __construct(
        private int $exercise_id,
        private bool $is_story,
        private array $entries = [],
    ) {}

    public function getExerciseId(): int
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
