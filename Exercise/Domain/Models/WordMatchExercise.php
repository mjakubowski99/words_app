<?php

namespace Exercise\Domain\Models;

use Shared\Enum\ExerciseType;
use Shared\Flashcard\ISessionFlashcardSummaries;
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

    public static function newFromSummaries(ISessionFlashcardSummaries $summaries, UserId $user_id): self
    {
        $exercise_entries = [];

        $i = 0;
        foreach ($summaries->getSummaries() as $summary) {
            $exercise_entries[] = WordMatchExerciseEntry::newFromSummary($summary, $i);
            $i++;
        }

        return new self(
            $summaries->hasStory() ? $summaries->getStoryId() : null,
            ExerciseId::noId(),
            $user_id,
            ExerciseStatus::NEW,
            $exercise_entries
        );
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