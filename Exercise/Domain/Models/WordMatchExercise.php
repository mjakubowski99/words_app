<?php

namespace Exercise\Domain\Models;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\UserId;

class WordMatchExercise extends Exercise
{
    public function __construct(
        private ?StoryId $story_id,
        ExerciseId $exercise_id,
        UserId $user_id,
        ExerciseStatus $status,
        array $exercise_entries,
    ) {
        parent::__construct($exercise_id, $user_id, $exercise_entries, $status, ExerciseType::WORD_MATCH);
    }

    public function getStoryId(): ?StoryId
    {
        return $this->story_id;
    }

    /** @return WordMatchExerciseEntry[] */
    public function getExerciseEntries(): array
    {
        return parent::getExerciseEntries();
    }
}