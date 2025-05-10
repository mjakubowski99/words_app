<?php

namespace Exercise\Application\Facades;

use Exercise\Application\Repositories\IUnscrambleWordExerciseReadRepository;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;

class ExerciseReadFacade implements IExerciseReadFacade
{
    public function __construct(
        private IUnscrambleWordExerciseReadRepository $repository,
    ) {}

    public function getUnscrambleWordExercise(ExerciseEntryId $id): IUnscrambleWordExerciseRead
    {
        return $this->repository->findByEntryId($id);
    }
}