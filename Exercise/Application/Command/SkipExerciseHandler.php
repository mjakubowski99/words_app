<?php

namespace Exercise\Application\Command;

use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\ValueObjects\ExerciseId;

class SkipExerciseHandler
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $repository,
    ) {}

    public function handle(ExerciseId $id): void
    {
        $exercise = $this->repository->find($id);

        $exercise->skipExercise();

        $this->repository->save($exercise);

        // copy ratings from previous flashcard ratings
    }
}