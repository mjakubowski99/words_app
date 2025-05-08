<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Repositories;

use Exercise\Application\Repositories\IUnscrambleWordExerciseReadRepository;
use Exercise\Infrastructure\Mappers\Postgres\UnscrambleWordExerciseReadMapper;
use Shared\Exercise\ExerciseTypes\IUnscrambleWordExerciseRead;
use Shared\Utils\ValueObjects\ExerciseId;

class UnscrambleWordExerciseReadRepository implements IUnscrambleWordExerciseReadRepository
{
    public function __construct(
        private UnscrambleWordExerciseReadMapper $mapper
    ) {}

    public function find(ExerciseId $id): IUnscrambleWordExerciseRead
    {
        return $this->mapper->find($id);
    }
}
