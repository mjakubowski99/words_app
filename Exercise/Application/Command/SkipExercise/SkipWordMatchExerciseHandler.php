<?php

namespace Exercise\Application\Command\SkipExercise;

use Exercise\Application\Repositories\IWordMatchExerciseRepository;
use Exercise\Domain\Models\Exercise;
use Exercise\Domain\Models\WordMatchExercise;
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
        if (!$exercise instanceof WordMatchExercise) {
            throw new \InvalidArgumentException('Expected instance of WordMatchExercise');
        }
        $this->repository->save($exercise);
    }
}