<?php

declare(strict_types=1);

namespace Exercise\Application\Command\SkipExercise;

use Exercise\Application\Repositories\Exercise\IWordMatchExerciseRepository;
use Exercise\Domain\Exceptions\InvalidExerciseTypeException;
use Exercise\Domain\Models\Exercise\Exercise;
use Exercise\Domain\Models\Exercise\WordMatchExercise;
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
            throw new InvalidExerciseTypeException();
        }
        $this->repository->save($exercise);
    }
}
