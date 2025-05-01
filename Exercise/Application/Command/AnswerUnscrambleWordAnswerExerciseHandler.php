<?php

namespace Exercise\Application\Command;

use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\Models\Answer;
use Exercise\Domain\Models\Exercise;
use Exercise\Domain\Models\UnscrambleWordAnswer;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Shared\Flashcard\IFlashcardFacade;

class AnswerUnscrambleWordAnswerExerciseHandler extends AbstractAnswerExerciseHandler
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $repository,
        private IFlashcardFacade $facade,
    ) {
        parent::__construct($this->facade);
    }

    protected function resolveExercise(ExerciseEntryId $id): Exercise
    {
        return $this->repository->findByAnswerEntryId($id);
    }

    protected function resolveAnswer(ExerciseEntryId $id, string $answer): Answer
    {
        return UnscrambleWordAnswer::fromString($id, $answer);
    }

    protected function save(Exercise $exercise): void
    {
        if (!$exercise instanceof UnscrambleWordsExercise) {
            throw new \InvalidArgumentException('Invalid exercise type');
        }
        $this->repository->save($exercise);
    }
}