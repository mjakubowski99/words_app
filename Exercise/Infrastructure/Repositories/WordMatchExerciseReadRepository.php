<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Exercise\Exercises\IWordMatchExerciseRead;
use Exercise\Application\Repositories\IWordMatchExerciseReadRepository;
use Exercise\Infrastructure\Mappers\Postgres\WordMatchExerciseReadMapper;

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
