<?php

declare(strict_types=1);

namespace Exercise\Application\Facades;

use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;
use Exercise\Application\Repositories\IUnscrambleWordExerciseReadRepository;

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
