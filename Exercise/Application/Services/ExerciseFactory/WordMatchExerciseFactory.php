<?php

namespace Exercise\Application\Services\ExerciseFactory;

use Exercise\Application\DTO\FlashcardExercise;
use Exercise\Application\Repositories\IWordMatchExercise;
use Exercise\Application\Repositories\IWordMatchExerciseRepository;
use Exercise\Domain\Models\ExerciseEntry;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\Models\WordMatchAnswer;
use Exercise\Domain\Models\WordMatchExercise;
use Exercise\Domain\Models\WordMatchExerciseEntry;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\UserId;

class WordMatchExerciseFactory implements IExerciseFactory
{
    public function __construct(
        private IWordMatchExerciseRepository $word_match_exercise_repository,
    ) {}

    public function make(ISessionFlashcardSummaries $summaries, UserId $user_id): array
    {
        $exercise_entries = [];

        foreach ($summaries->getSummaries() as $summary) {
            $exercise_entries[] = new WordMatchExerciseEntry(
                $summary->getBackWord(),
                $summary->getFrontWord(),
                $summary->getStorySentence() ?? $summary->getBackContext(),
                ExerciseEntryId::noId(),
                ExerciseId::noId(),
                WordMatchAnswer::fromString(ExerciseEntryId::noId(), $summary->getBackWord()),
                null,
                null
            );
        }

        $exercise = new WordMatchExercise(
            $summaries->hasStory() ? $summaries->getStoryId() : null,
            ExerciseId::noId(),
            $user_id,
            ExerciseStatus::NEW,
            $exercise_entries
        );

        $exercise_id = $this->word_match_exercise_repository->create($exercise);

        $exercise = $this->word_match_exercise_repository->find($exercise_id);

        return FlashcardExercise::newCollection($summaries, $exercise);
    }
}