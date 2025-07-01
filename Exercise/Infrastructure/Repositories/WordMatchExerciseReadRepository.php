<?php

namespace Exercise\Infrastructure\Repositories;

use Exercise\Application\Repositories\IWordMatchExerciseReadRepository;
use Exercise\Infrastructure\Mappers\Postgres\WordMatchExerciseReadMapper;
use Shared\Exercise\Exercises\IWordMatchExerciseRead;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class WordMatchExerciseReadRepository implements IWordMatchExerciseReadRepository
{
    public function __construct(
        private WordMatchExerciseReadMapper $mapper
    ) {}

    public function findByEntryId(ExerciseEntryId $id): IWordMatchExerciseRead
    {
        return $this->mapper->findByEntryId($id);
    }
}