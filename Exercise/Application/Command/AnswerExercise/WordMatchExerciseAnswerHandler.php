<?php

namespace Exercise\Application\Command\AnswerExercise;

use Exercise\Application\Repositories\IWordMatchExerciseRepository;
use Exercise\Domain\Models\Exercise;
use Exercise\Domain\Models\WordMatchExercise;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\ExerciseId;

class WordMatchExerciseAnswerHandler extends AbstractExerciseAnswerHandler
{
    public function __construct(
        private IWordMatchExerciseRepository $repository,
        IFlashcardFacade $facade,
    ) {
        parent::__construct($facade);
    }

    protected function resolveExercise(ExerciseId $exercise_id): Exercise
    {
        return $this->repository->find($exercise_id);
    }

    protected function save(Exercise $exercise): void
    {
        if (!$exercise instanceof WordMatchExercise) {
            throw new \InvalidArgumentException('Invalid exercise type');
        }
        $this->repository->save($exercise);
    }
}