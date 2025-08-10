<?php

declare(strict_types=1);

namespace Exercise\Domain\Models;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Flashcard\ISessionFlashcardSummaries;

class WordMatchExercise extends Exercise
{
    private array $word_match_entries;

    public function __construct(
        private ?StoryId $story_id,
        ExerciseId $exercise_id,
        UserId $user_id,
        ExerciseStatus $status,
        array $exercise_entries,
        private array $options,
    ) {
        parent::__construct($exercise_id, $user_id, $exercise_entries, $status, ExerciseType::WORD_MATCH);
        $this->word_match_entries = $exercise_entries;
    }

    public static function newFromSummaries(ISessionFlashcardSummaries $summaries, UserId $user_id): self
    {
        $exercise_entries = [];

        foreach ($summaries->getSummaries() as $summary) {
            $exercise_entries[] = WordMatchExerciseEntry::newFromSummary($summary);
        }

        $options = [];
        foreach ($summaries->getAnswerOptions() as $option) {
            $options[] = $option->getOption();
        }

        return new self(
            $summaries->hasStory() ? $summaries->getStoryId() : null,
            ExerciseId::noId(),
            $user_id,
            ExerciseStatus::NEW,
            $exercise_entries,
            $options
        );
    }

    public function getStoryId(): ?StoryId
    {
        return $this->story_id;
    }

    /** @return WordMatchExerciseEntry[] */
    public function getExerciseEntries(): array
    {
        return $this->word_match_entries;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
