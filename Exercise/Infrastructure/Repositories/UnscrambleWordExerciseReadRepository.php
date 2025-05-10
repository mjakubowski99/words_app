<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;
use Exercise\Application\Repositories\IUnscrambleWordExerciseReadRepository;
use Exercise\Infrastructure\Mappers\Postgres\UnscrambleWordExerciseReadMapper;

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
