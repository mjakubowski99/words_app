<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Repositories;

use Exercise\Application\Repositories\ExerciseRead\IUnscrambleWordExerciseReadRepository;
use Exercise\Infrastructure\Mappers\Postgres\UnscrambleWordExerciseReadMapper;
use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class UnscrambleWordExerciseReadRepository implements IUnscrambleWordExerciseReadRepository
{
    public function __construct(
        private UnscrambleWordExerciseReadMapper $mapper
    ) {}

    public function findByEntryId(ExerciseEntryId $id): IUnscrambleWordExerciseRead
    {
        return $this->mapper->findByEntryId($id);
    }
}
