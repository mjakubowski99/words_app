<?php

namespace Exercise\Application\Command;

use Exercise\Domain\Models\Answer;
use Exercise\Domain\Models\Exercise;
use Exercise\Domain\Models\ExerciseEntry;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Shared\Flashcard\IFlashcardFacade;

abstract class AbstractExerciseHandler
{
    public function __construct(
        private IFlashcardFacade $facade,
    ) {}

    public function handle(ExerciseEntryId $id, string $user_answer): void
    {
        $exercise = $this->resolveExercise($id);

        $this->processAnswer($exercise, $this->resolveAnswer($id, $user_answer));

        $this->save($exercise);
    }

    protected abstract function resolveExercise(ExerciseEntryId $id): Exercise;
    protected abstract function resolveAnswer(ExerciseEntryId $id, string $answer): Answer;
    protected abstract function save(Exercise $exercise): void;

    protected function processAnswer(Exercise $exercise, Answer $answer): void
    {
        $exercise->assessAnswer($answer);

        if ($exercise->getStatus() !== ExerciseStatus::DONE) {
            return;
        }

        $update_data = [];
        /** @var ExerciseEntry $entry */
        foreach ($exercise->getExerciseEntries() as $entry) {
            $update_data[$entry->getSessionFlashcardId()->getValue()] = $entry->getScore();
        }

        $this->facade->updateRatings($update_data);
    }
}