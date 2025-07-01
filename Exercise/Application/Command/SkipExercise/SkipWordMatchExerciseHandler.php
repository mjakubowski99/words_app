<?php

namespace Exercise\Application\Command\SkipExercise;

use Exercise\Application\Repositories\IWordMatchExerciseRepository;
use Exercise\Domain\Models\Exercise;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\ExerciseId;

class SkipWordMatchExerciseHandler extends AbstractSkipExerciseHandler
{
    public function __construct(
        private IWordMatchExerciseRepository $repository,
        IFlashcardFacade $facade,
    ) {
        parent::__construct($facade);
    }

    protected function findExercise(ExerciseId $id): Exercise
    {
        return $this->repository->find($id);
    }

    protected function saveExercise(Exercise $exercise): void
    {
        $this->repository->save($exercise);
    }
}